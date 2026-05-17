<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKolektor extends Model
{
    protected $table = 'master_kolektor';
    protected $fillable = ['nama_kolektor', 'alamat', 'kecamatan', 'desa', 'kontak', 'lokasi'];

    public function pelanggan()
    {
        return $this->hasMany(MasterPelanggan::class, 'kolektor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'kolektor_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'kolektor_id');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'kolektor_id');
    }
}
