<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterPelanggan;
use App\Models\MasterDusun;
use App\Models\MasterBulanan;
use App\Models\MasterKolektor;
use App\Models\MasterTeknisi;
use App\Models\MasterPenagih;
use App\Models\Tagihan;
use App\Models\User;
use App\Support\AdminDesaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PelangganBaruNotification;
use Carbon\Carbon;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $isKolektorOnly = auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin');
        $isAdminDesaOnly = AdminDesaScope::isAdminDesaOnly();
        $query = MasterPelanggan::with(['dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih']);

        if ($isKolektorOnly) {
            $query->where('kolektor_id', auth()->user()->kolektor_id);
        } elseif ($isAdminDesaOnly) {
            AdminDesaScope::applyPelangganScope($query);
        }

        $query->when($request->search, function($q, $v) {
            $q->where('nama_pelanggan', 'like', "%$v%");
        });

        if (!$isAdminDesaOnly) {
            $query->when($request->kecamatan, function($q, $v) {
                $q->where('kecamatan', $v);
            })->when($request->desa, function($q, $v) {
                $q->where('desa', $v);
            });
        }

        $query->when($request->dusun_id, function($q, $v) {
            $q->where('dusun_id', $v);
        })->when($request->status_alat, function($q, $v) {
            $q->where('status_alat', $v);
        });

        if (!$isKolektorOnly) {
            $query->when($request->kolektor_id, function($q, $v) {
                $q->where('kolektor_id', $v);
            });
        }

        if ($request->sort_nama === 'nama_asc') {
            $query->orderBy('nama_pelanggan', 'asc');
        } elseif ($request->sort_nama === 'nama_desc') {
            $query->orderBy('nama_pelanggan', 'desc');
        } else {
            $query->latest();
        }

        $pelanggan = $query->paginate(20)->withQueryString();

        $scopeQuery = MasterPelanggan::query();
        if ($isKolektorOnly) {
            $scopeQuery->where('kolektor_id', auth()->user()->kolektor_id);
        } elseif ($isAdminDesaOnly) {
            AdminDesaScope::applyPelangganScope($scopeQuery);
        } elseif ($request->kolektor_id) {
            $scopeQuery->where('kolektor_id', $request->kolektor_id);
        }

        $kecamatanList = (clone $scopeQuery)
            ->select('kecamatan')
            ->whereNotNull('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan')
            ->filter()
            ->values()
            ->toArray();

        $desaOptions = (clone $scopeQuery)
            ->select('kecamatan', 'desa')
            ->whereNotNull('kecamatan')
            ->whereNotNull('desa')
            ->distinct()
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get()
            ->map(fn ($row) => [
                'kecamatan' => $row->kecamatan,
                'desa' => $row->desa,
            ])
            ->values();

        $dusunOptionsQuery = MasterDusun::query()
            ->select('id', 'dusun', 'desa', 'kecamatan');

        if ($isAdminDesaOnly) {
            AdminDesaScope::applyDusunScope($dusunOptionsQuery);
        } elseif (!empty($kecamatanList)) {
            $dusunOptionsQuery->whereIn('kecamatan', $kecamatanList);
        } else {
            $dusunOptionsQuery->whereRaw('1 = 0');
        }

        $dusunOptions = $dusunOptionsQuery
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->orderBy('dusun')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'dusun' => $row->dusun,
                'desa' => $row->desa,
                'kecamatan' => $row->kecamatan,
            ])
            ->values();

        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $teknisi = MasterTeknisi::all();
        $penagih = MasterPenagih::all();
        
        if ($isKolektorOnly) {
            $kolektor = MasterKolektor::where('id', auth()->user()->kolektor_id)->get();
        } elseif ($isAdminDesaOnly) {
            $kolektorQuery = MasterKolektor::query();
            AdminDesaScope::applyWilayahMasterScope($kolektorQuery);
            $kolektor = $kolektorQuery->get();
        } else {
            $kolektor = MasterKolektor::all();
        }

        return view('master.pelanggan.index', compact(
            'pelanggan',
            'kecamatanList',
            'desaOptions',
            'dusunOptions',
            'dusun',
            'bulanan',
            'kolektor',
            'teknisi',
            'penagih',
            'isAdminDesaOnly'
        ));
    }

    public function create()
    {
        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $teknisi = MasterTeknisi::all();
        $penagih = MasterPenagih::all();
        
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $kolektor = MasterKolektor::where('id', auth()->user()->kolektor_id)->get();
        } else {
            $kolektor = MasterKolektor::all();
        }
        $penagih = MasterPenagih::all();
        return view('master.pelanggan.create', compact('dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pelanggan'    => 'required|string|max:150',
            'kecamatan'         => 'nullable|string|max:100',
            'desa'              => 'nullable|string|max:100',
            'desa_kode'         => 'nullable|string|max:13|exists:wilayah,kode',
            'dusun_id'          => 'nullable|exists:master_dusun,id',
            'bulanan_id'        => 'nullable|exists:master_bulanan,id',
            'tanggal_pemasangan'=> 'nullable|date',
            'kolektor_id'       => 'nullable|exists:master_kolektor,id',
            'teknisi_id'        => 'nullable|exists:master_teknisi,id',
            'penagih_id'        => 'nullable|exists:master_penagih,id',
            'status_alat'       => 'required|in:beli,pinjam',
            'is_active'         => 'nullable|boolean',
            'kontak'            => 'nullable|string|max:50',
            'lokasi'            => 'nullable|string',
        ]);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $data['kolektor_id'] = auth()->user()->kolektor_id;
        }
        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $data = $this->applyActiveStatusTransition($data);

        if (MasterPelanggan::where('nama_pelanggan', $data['nama_pelanggan'])
            ->where('kolektor_id', $data['kolektor_id'])
            ->exists()
        ) {
            return back()->withErrors(['nama_pelanggan' => 'Nama pelanggan "' . $data['nama_pelanggan'] . '" sudah terdaftar untuk kolektor ini.'])->withInput();
        }

        DB::transaction(function () use ($data, &$pelanggan) {
            $pelanggan = MasterPelanggan::create($data);
            $this->buatTagihanOtomatis($pelanggan);
        });

        $superadmins = User::role('superadmin')->get();
        Notification::send($superadmins, new PelangganBaruNotification($pelanggan));

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan dan tagihan bulan ini telah dibuat otomatis.');
    }

    public function edit(MasterPelanggan $pelanggan)
    {
        $this->authorizePelangganAccess($pelanggan);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $kolektor = MasterKolektor::where('id', auth()->user()->kolektor_id)->get();
        } else {
            $kolektor = MasterKolektor::all();
        }

        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $teknisi = MasterTeknisi::all();
        $penagih = MasterPenagih::all();
        return view('master.pelanggan.edit', compact('pelanggan', 'dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih'));
    }

    public function update(Request $request, MasterPelanggan $pelanggan)
    {
        $this->authorizePelangganAccess($pelanggan);
        $data = $request->validate([
            'nama_pelanggan'    => 'required|string|max:150',
            'kecamatan'         => 'nullable|string|max:100',
            'desa'              => 'nullable|string|max:100',
            'desa_kode'         => 'nullable|string|max:13|exists:wilayah,kode',
            'dusun_id'          => 'nullable|exists:master_dusun,id',
            'bulanan_id'        => 'nullable|exists:master_bulanan,id',
            'tanggal_pemasangan'=> 'nullable|date',
            'kolektor_id'       => 'nullable|exists:master_kolektor,id',
            'teknisi_id'        => 'nullable|exists:master_teknisi,id',
            'penagih_id'        => 'nullable|exists:master_penagih,id',
            'status_alat'       => 'required|in:beli,pinjam',
            'is_active'         => 'nullable|boolean',
            'kontak'            => 'nullable|string|max:50',
            'lokasi'            => 'nullable|string',
        ]);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $data['kolektor_id'] = auth()->user()->kolektor_id;
        }
        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $data = $this->applyActiveStatusTransition($data, $pelanggan);

        if (MasterPelanggan::where('nama_pelanggan', $data['nama_pelanggan'])
            ->where('kolektor_id', $data['kolektor_id'])
            ->where('id', '!=', $pelanggan->id)
            ->exists()
        ) {
            return back()->withErrors(['nama_pelanggan' => 'Nama pelanggan "' . $data['nama_pelanggan'] . '" sudah terdaftar untuk kolektor ini.'])->withInput();
        }

        $pelanggan->update($data);

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(MasterPelanggan $pelanggan)
    {
        $this->authorizePelangganAccess($pelanggan);
        $pelanggan->delete();
        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function nonaktifkan(MasterPelanggan $pelanggan)
    {
        $this->authorizePelangganAccess($pelanggan);

        if (!$pelanggan->is_active) {
            return redirect()->route('master.pelanggan.index')->with('info', 'Pelanggan sudah berstatus nonaktif.');
        }

        $pelanggan->update([
            'is_active' => false,
            'nonaktif_at' => now(),
            'aktif_kembali_at' => null,
        ]);

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil dinonaktifkan.');
    }

    public function aktifkan(MasterPelanggan $pelanggan)
    {
        $this->authorizePelangganAccess($pelanggan);

        if ($pelanggan->is_active) {
            return redirect()->route('master.pelanggan.index')->with('info', 'Pelanggan sudah berstatus aktif.');
        }

        $pelanggan->update([
            'is_active' => true,
            'aktif_kembali_at' => now(),
        ]);

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil diaktifkan kembali.');
    }

    /**
     * Buat tagihan otomatis untuk bulan berjalan.
     * Nominal diambil dari paket bulanan pelanggan.
     * Jatuh tempo mengikuti tanggal_pemasangan.
     */
    private function buatTagihanOtomatis(MasterPelanggan $pelanggan): void
    {
        if (!$pelanggan->is_active) {
            return;
        }

        $now     = Carbon::now();
        $bulan   = (int) $now->month;
        $tahun   = (int) $now->year;
        $nominal = 0;

        if ($pelanggan->bulanan_id && $pelanggan->bulanan) {
            $nominal = $pelanggan->bulanan->nominal;
        }

        // Hindari duplikasi (unique constraint pelanggan_id+bulan+tahun)
        Tagihan::firstOrCreate(
            ['pelanggan_id' => $pelanggan->id, 'bulan' => $bulan, 'tahun' => $tahun],
            [
                'kolektor_id' => $pelanggan->kolektor_id,
                'nominal'     => $nominal,
                'status'      => 'belum_lunas',
                'created_by'  => auth()->id(),
            ]
        );
    }

    private function applyActiveStatusTransition(array $data, ?MasterPelanggan $current = null): array
    {
        $isActive = (bool) ($data['is_active'] ?? true);

        if (!$current) {
            if ($isActive) {
                $data['nonaktif_at'] = null;
                $data['aktif_kembali_at'] = null;
            } else {
                $data['nonaktif_at'] = now();
                $data['aktif_kembali_at'] = null;
            }

            return $data;
        }

        if ($current->is_active && !$isActive) {
            $data['nonaktif_at'] = now();
            $data['aktif_kembali_at'] = null;
        } elseif (!$current->is_active && $isActive) {
            $data['aktif_kembali_at'] = now();
        }

        return $data;
    }

    private function authorizePelangganAccess(MasterPelanggan $pelanggan): void
    {
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            if ($pelanggan->kolektor_id !== auth()->user()->kolektor_id) {
                abort(403, 'Anda tidak memiliki akses ke pelanggan ini.');
            }
        }

        if (AdminDesaScope::isAdminDesaOnly() && !AdminDesaScope::pelangganInScope($pelanggan)) {
            abort(403, 'Anda tidak memiliki akses ke pelanggan ini.');
        }
    }

}
