<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPenagih extends Model
{
    protected $table = 'master_penagih';
    protected $fillable = ['nama_penagih', 'alamat', 'kecamatan', 'desa'];

    public function pelanggan()
    {
        return $this->hasMany(MasterPelanggan::class, 'penagih_id');
    }
}
