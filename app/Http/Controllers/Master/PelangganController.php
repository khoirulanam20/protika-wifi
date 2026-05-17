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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPelanggan::with(['dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih']);

        $query->when($request->search, function($q, $v) {
            $q->where('nama_pelanggan', 'like', "%$v%");
        })->when($request->kecamatan, function($q, $v) {
            $q->where('kecamatan', $v);
        })->when($request->status_alat, function($q, $v) {
            $q->where('status_alat', $v);
        });

        $pelanggan = $query->latest()->paginate(20)->withQueryString();
        $kecamatanList = MasterPelanggan::distinct()->pluck('kecamatan')->filter()->toArray();

        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $kolektor = MasterKolektor::all();
        $teknisi = MasterTeknisi::all();
        $penagih = MasterPenagih::all();

        return view('master.pelanggan.index', compact(
            'pelanggan', 'kecamatanList', 'dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih'
        ));
    }

    public function create()
    {
        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $kolektor = MasterKolektor::all();
        $teknisi = MasterTeknisi::all();
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

        DB::transaction(function () use ($data) {
            $pelanggan = MasterPelanggan::create($data);
            $this->buatTagihanOtomatis($pelanggan);
        });

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan dan tagihan bulan ini telah dibuat otomatis.');
    }

    public function edit(MasterPelanggan $pelanggan)
    {
        $dusun   = MasterDusun::all();
        $bulanan = MasterBulanan::all();
        $kolektor = MasterKolektor::all();
        $teknisi = MasterTeknisi::all();
        $penagih = MasterPenagih::all();
        return view('master.pelanggan.edit', compact('pelanggan', 'dusun', 'bulanan', 'kolektor', 'teknisi', 'penagih'));
    }

    public function update(Request $request, MasterPelanggan $pelanggan)
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

        $pelanggan->update($data);

        return redirect()->route('master.pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(MasterPelanggan $pelanggan)
    {
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
