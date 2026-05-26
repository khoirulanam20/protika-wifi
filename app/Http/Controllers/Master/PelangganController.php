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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PelangganBaruNotification;
use Carbon\Carbon;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPelanggan::with(['dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih']);
        
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $query->where('kolektor_id', auth()->user()->kolektor_id);
        }

        $query->when($request->search, function($q, $v) {
            $q->where('nama_pelanggan', 'like', "%$v%");
        })->when($request->kecamatan, function($q, $v) {
            $q->where('kecamatan', $v);
        })->when($request->desa, function($q, $v) {
            $q->where('desa', $v);
        })->when($request->status_alat, function($q, $v) {
            $q->where('status_alat', $v);
        });

        if (!auth()->user()->hasRole('kolektor')) {
            $query->when($request->kolektor_id, function($q, $v) {
                $q->where('kolektor_id', $v);
            });
        }

        $pelanggan = $query->latest()->paginate(20)->withQueryString();
        $kecamatanList = MasterPelanggan::distinct()->pluck('kecamatan')->filter()->toArray();
        $desaList = MasterPelanggan::distinct()->pluck('desa')->filter()->toArray();

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

        return view('master.pelanggan.index', compact(
            'pelanggan', 'kecamatanList', 'desaList', 'dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih'
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
            'dusun_id'          => 'nullable|exists:master_dusun,id',
            'bulanan_id'        => 'nullable|exists:master_bulanan,id',
            'tanggal_pemasangan'=> 'nullable|date',
            'kolektor_id'       => 'nullable|exists:master_kolektor,id',
            'teknisi_id'        => 'nullable|exists:master_teknisi,id',
            'penagih_id'        => 'nullable|exists:master_penagih,id',
            'status_alat'       => 'required|in:beli,pinjam',
            'kontak'            => 'nullable|string|max:50',
            'lokasi'            => 'nullable|string',
        ]);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $data['kolektor_id'] = auth()->user()->kolektor_id;
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
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            if ($pelanggan->kolektor_id !== auth()->user()->kolektor_id) {
                abort(403, 'Anda tidak memiliki akses ke pelanggan ini.');
            }
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
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            if ($pelanggan->kolektor_id !== auth()->user()->kolektor_id) {
                abort(403, 'Anda tidak memiliki akses ke pelanggan ini.');
            }
        }
        $data = $request->validate([
            'nama_pelanggan'    => 'required|string|max:150',
            'kecamatan'         => 'nullable|string|max:100',
            'desa'              => 'nullable|string|max:100',
            'dusun_id'          => 'nullable|exists:master_dusun,id',
            'bulanan_id'        => 'nullable|exists:master_bulanan,id',
            'tanggal_pemasangan'=> 'nullable|date',
            'kolektor_id'       => 'nullable|exists:master_kolektor,id',
            'teknisi_id'        => 'nullable|exists:master_teknisi,id',
            'penagih_id'        => 'nullable|exists:master_penagih,id',
            'status_alat'       => 'required|in:beli,pinjam',
            'kontak'            => 'nullable|string|max:50',
            'lokasi'            => 'nullable|string',
        ]);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $data['kolektor_id'] = auth()->user()->kolektor_id;
        }

        $pelanggan->update($data);

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(MasterPelanggan $pelanggan)
    {
        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            if ($pelanggan->kolektor_id !== auth()->user()->kolektor_id) {
                abort(403, 'Anda tidak memiliki akses ke pelanggan ini.');
            }
        }
        $pelanggan->delete();
        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Buat tagihan otomatis untuk bulan berjalan.
     * Nominal diambil dari paket bulanan pelanggan.
     * Jatuh tempo mengikuti tanggal_pemasangan.
     */
    private function buatTagihanOtomatis(MasterPelanggan $pelanggan): void
    {
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
}
