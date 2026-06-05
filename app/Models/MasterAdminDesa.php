<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAdminDesa extends Model
{
    protected $table = 'master_admin_desa';

    protected $fillable = [
        'nama_admin',
        'alamat',
        'kecamatan',
        'desa',
        'desa_kode',
        'kontak',
        'lokasi',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'desa_kode', 'kode');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'admin_desa_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'admin_desa_id');
    }

    public function pelangganQuery()
    {
        return MasterPelanggan::query()->where(function ($q) {
            $q->where('desa_kode', $this->desa_kode)
                ->orWhereHas('dusun', fn ($d) => $d->where('desa_kode', $this->desa_kode));
        });
    }
}
