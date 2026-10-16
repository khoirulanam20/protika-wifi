<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    protected $table = 'perbaikan';
    protected $fillable = ['user_id', 'pelanggan_id', 'tanggal_perbaikan', 'lokasi', 'alamat', 'deskripsi', 'peralatan', 'foto'];
    protected $casts = ['tanggal_perbaikan' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(MasterPelanggan::class, 'pelanggan_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function getMapsUrlAttribute(): string
    {
        return 'https://www.google.com/maps?q=' . urlencode(str_replace(' ', '', $this->lokasi));
    }
}
