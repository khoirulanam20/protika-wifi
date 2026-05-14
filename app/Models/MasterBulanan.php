<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterBulanan extends Model
{
    protected $table = 'master_bulanan';
    protected $fillable = ['nominal', 'terbilang'];

    public function pelanggan()
    {
        return $this->hasMany(MasterPelanggan::class, 'bulanan_id');
    }
}
