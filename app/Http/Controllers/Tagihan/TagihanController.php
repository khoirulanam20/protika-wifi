<?php

namespace App\Http\Controllers\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\MasterPelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TagihanTerbayarNotification;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $now   = Carbon::now();
        $bulan = $request->bulan ?? $now->month;
        $tahun = $request->tahun ?? $now->year;

        $query = Tagihan::with(['pelanggan.bulanan', 'pelanggan', 'kolektor']);

        if (auth()->user()->hasRole('kolektor')) {
            $query->where('kolektor_id', auth()->user()->kolektor_id);
        }

        // Generate tagihan otomatis yang belum ada untuk pelanggan aktif
        $this->generateTagihanBulanIni($now, auth()->user());

        $query->when($bulan,              fn($q, $v) => $q->where('bulan',  $v))
              ->when($tahun,              fn($q, $v) => $q->where('tahun',  $v))
              ->when($request->status,   fn($q, $v) => $q->where('status', $v))
              ->when($request->search,   fn($q, $v) => $q->whereHas('pelanggan', fn($p) => $p->where('nama_pelanggan', 'like', "%$v%")))
              ->when($request->kecamatan, fn($q, $v) => $q->whereHas('pelanggan', fn($p) => $p->where('kecamatan', $v)))
              ->when($request->kolektor_id && auth()->user()->hasRole('superadmin'), fn($q, $v) => $q->where('kolektor_id', $v));

        $tagihan = $query->latest()->paginate(20)->withQueryString();

        // Hitung tunggakan (tagihan belum lunas/sebagian dari bulan-bulan sebelumnya)
        $tunggakanQuery = Tagihan::with('pelanggan')
            ->whereIn('status', ['belum_lunas', 'sebagian'])
            ->where(function ($q) use ($now) {
                $q->where('tahun', '<', $now->year)
                  ->orWhere(function ($q2) use ($now) {
                      $q2->where('tahun', $now->year)->where('bulan', '<', $now->month);
                  });
            });

        $kecamatanQuery = MasterPelanggan::distinct()->select('kecamatan')->whereNotNull('kecamatan');

        if (auth()->user()->hasRole('kolektor')) {
            $tunggakanQuery->where('kolektor_id', auth()->user()->kolektor_id);
            $kecamatanQuery->where('kolektor_id', auth()->user()->kolektor_id);
        }

        $totalTunggakan = $tunggakanQuery->count();
        
        $kecamatanList = $kecamatanQuery->pluck('kecamatan')->filter()->toArray();
        $kolektorList = auth()->user()->hasRole('superadmin') ? \App\Models\MasterKolektor::all() : [];

        return view('tagihan.index', compact('tagihan', 'bulan', 'tahun', 'totalTunggakan', 'kecamatanList', 'kolektorList'));
    }

    public function create()
    {
        // Redirect ke index — tagihan dibuat otomatis
        return redirect()->route('tagihan.index');
    }

    public function store(Request $request)
    {
        // Tidak digunakan lagi — tagihan dibuat otomatis
        return redirect()->route('tagihan.index')->with('info', 'Tagihan dibuat otomatis setiap bulan.');
    }

    public function edit(Tagihan $tagihan)
    {
        return view('tagihan.edit', compact('tagihan'));
    }

    /**
     * Kolektor hanya bisa update: status, nominal_bayar, tanggal_bayar, keterangan.
     * Aturan sebagian: nominal_bayar harus < sisa tagihan dan > 0.
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $rules = [
            'status'       => 'required|in:lunas,belum_lunas,sebagian',
            'tanggal_bayar'=> 'nullable|date',
            'keterangan'   => 'nullable|string|max:500',
        ];

        $sisa = $tagihan->sisa_tagihan;

        // Nominal bayar hanya wajib jika lunas atau sebagian
        if (in_array($request->status, ['lunas', 'sebagian'])) {
            $rules['nominal_bayar'] = 'required|numeric|min:1';

            // Jika sebagian: harus lebih kecil dari SISA tagihan
            if ($request->status === 'sebagian') {
                $rules['nominal_bayar'] = 'required|numeric|min:1|lt:' . $sisa;
            }
        }

        $validated = $request->validate($rules);

        $updateData = [
            'status'        => $validated['status'],
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? null,
            'keterangan'    => $validated['keterangan'] ?? null,
        ];

        // Hitung total terbayar baru
        if (isset($validated['nominal_bayar']) && in_array($validated['status'], ['lunas', 'sebagian'])) {
            // Jika status lunas dari modal otomatis kirim nominal sisa tagihan
            $updateData['terbayar'] = $tagihan->terbayar + $validated['nominal_bayar'];
            
            // Safety check: jika lunas, pastikan terbayar penuh
            if ($validated['status'] === 'lunas') {
                $updateData['terbayar'] = $tagihan->nominal;
            }
        } elseif ($validated['status'] === 'belum_lunas') {
            // Jika diubah jadi belum lunas (reset)
            $updateData['terbayar'] = 0;
        }

        $tagihan->update($updateData);

        if (in_array($tagihan->status, ['lunas', 'sebagian'])) {
            $this->notifyTagihanTerbayar($tagihan);
        }

        return redirect()->route('tagihan.index')->with('success', 'Status tagihan berhasil diperbarui.');
    }

    /**
     * Quick-pay: langsung tandai lunas via checkbox.
     * Otomatis set nominal terbayar = nominal penuh, tanggal bayar = hari ini.
     */
    public function lunaskanCepat(Tagihan $tagihan)
    {
        // Hanya bisa dilunaskan jika belum lunas
        if ($tagihan->status === 'lunas') {
            return redirect()->back()->with('info', 'Tagihan sudah lunas.');
        }

        $tagihan->update([
            'status'        => 'lunas',
            'terbayar'      => $tagihan->nominal,
            'tanggal_bayar' => now()->toDateString(),
        ]);

        $this->notifyTagihanTerbayar($tagihan);

        return redirect()->back()->with('success', 'Tagihan ' . $tagihan->pelanggan->nama_pelanggan . ' berhasil dilunaskan.');
    }

    /**
     * Batal Lunas: reset tagihan kembali ke belum_lunas.
     */
    public function batalLunas(Tagihan $tagihan)
    {
        if ($tagihan->status !== 'lunas') {
            return redirect()->back()->with('info', 'Tagihan belum berstatus lunas.');
        }

        $tagihan->update([
            'status'        => 'belum_lunas',
            'terbayar'      => 0,
            'tanggal_bayar' => null,
            'keterangan'    => null,
        ]);

        return redirect()->back()->with('info', 'Pelunasan tagihan ' . $tagihan->pelanggan->nama_pelanggan . ' berhasil dibatalkan.');
    }

    /**
     * Bulk Quick-pay: tandai banyak lunas sekaligus.
     */
    public function lunaskanBanyak(Request $request)
    {
        $request->validate([
            'tagihan_ids'   => 'required|array',
            'tagihan_ids.*' => 'exists:tagihan,id'
        ]);

        $tagihans = Tagihan::whereIn('id', $request->tagihan_ids)
            ->where('status', '!=', 'lunas')
            ->get();

        $count = 0;
        foreach ($tagihans as $tagihan) {
            $tagihan->update([
                'status'        => 'lunas',
                'terbayar'      => $tagihan->nominal,
                'tanggal_bayar' => now()->toDateString(),
            ]);
            $this->notifyTagihanTerbayar($tagihan);
            $count++;
        }

        return redirect()->back()->with('success', $count . ' tagihan berhasil dilunaskan.');
    }

    public function destroy(Tagihan $tagihan)
    {
        // Hanya superadmin
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    // -----------------------------------------------------------------------
    // Auto-generate tagihan untuk semua pelanggan aktif di bulan berjalan
    // -----------------------------------------------------------------------
    private function generateTagihanBulanIni(Carbon $now, $user): void
    {
        $bulan = $now->month;
        $tahun = $now->year;

        $query = MasterPelanggan::with('bulanan')
            ->whereNotNull('kolektor_id')
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('aktif_kembali_at')
                    ->orWhereDate('aktif_kembali_at', '<=', $now->toDateString());
            });

        if ($user->hasRole('kolektor')) {
            $query->where('kolektor_id', $user->kolektor_id);
        }

        $pelangganList = $query->get();

        foreach ($pelangganList as $pelanggan) {
            Tagihan::firstOrCreate(
                ['pelanggan_id' => $pelanggan->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'kolektor_id' => $pelanggan->kolektor_id,
                    'nominal'     => $pelanggan->bulanan?->nominal ?? 0,
                    'status'      => 'belum_lunas',
                    'created_by'  => $user->id,
                ]
            );
        }
    }

    /**
     * Helper to notify superadmin and kolektor when tagihan is paid
     */
    private function notifyTagihanTerbayar(Tagihan $tagihan): void
    {
        $superadmins = User::role('superadmin')->get();
        Notification::send($superadmins, new TagihanTerbayarNotification($tagihan));
        
        if ($tagihan->kolektor_id) {
            $kolektors = User::where('kolektor_id', $tagihan->kolektor_id)->get();
            $kolektorsToNotify = $kolektors->reject(fn($user) => $superadmins->contains('id', $user->id));
            if ($kolektorsToNotify->count() > 0) {
                Notification::send($kolektorsToNotify, new TagihanTerbayarNotification($tagihan));
            }
        }
    }
}
