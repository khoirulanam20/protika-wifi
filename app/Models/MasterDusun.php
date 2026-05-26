<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDusun extends Model
{
    protected $table = 'master_dusun';
    protected $fillable = ['kecamatan', 'desa', 'desa_kode', 'dusun'];

    public function pelanggan()
    {
        return $this->hasMany(MasterPelanggan::class, 'dusun_id');
    }

    public function wilayahDesa()
    {
        return $this->belongsTo(Wilayah::class, 'desa_kode', 'kode');
    }
}
