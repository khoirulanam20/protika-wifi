<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPelanggan extends Model
{
    protected $table = 'master_pelanggan';
    protected $fillable = [
        'nama_pelanggan', 'kecamatan', 'desa', 'desa_kode', 'dusun_id', 'bulanan_id',
        'tanggal_pemasangan', 'kolektor_id', 'teknisi_id', 'penagih_id', 'status_alat',
        'is_active', 'nonaktif_at', 'aktif_kembali_at', 'kontak', 'lokasi'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nonaktif_at' => 'datetime',
        'aktif_kembali_at' => 'datetime',
    ];

    public function dusun()    { return $this->belongsTo(MasterDusun::class, 'dusun_id'); }
    public function bulanan()  { return $this->belongsTo(MasterBulanan::class, 'bulanan_id'); }
    public function kolektor() { return $this->belongsTo(MasterKolektor::class, 'kolektor_id'); }
    public function teknisi()  { return $this->belongsTo(MasterTeknisi::class, 'teknisi_id'); }
    public function penagih()  { return $this->belongsTo(MasterPenagih::class, 'penagih_id'); }
    public function tagihan()  { return $this->hasMany(Tagihan::class, 'pelanggan_id'); }
}
