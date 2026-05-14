<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTeknisi extends Model
{
    protected $table = 'master_teknisi';
    protected $fillable = ['nama_teknisi', 'alamat', 'kecamatan', 'desa'];

    public function pelanggan()
    {
        return $this->hasMany(MasterPelanggan::class, 'teknisi_id');
    }
}
