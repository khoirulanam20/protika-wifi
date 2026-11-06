<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Perbaikan extends Model
{
    protected $table = 'perbaikan';
    protected $fillable = ['user_id', 'pelanggan_id', 'tanggal_perbaikan', 'lokasi', 'alamat', 'deskripsi', 'peralatan', 'foto', 'dibayar_at'];
    protected $casts = ['tanggal_perbaikan' => 'datetime', 'dibayar_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(MasterPelanggan::class, 'pelanggan_id');
    }

    /** Alamat dari koordinat "lat,lng" via Nominatim; null jika gagal. */
    public static function alamatDariLokasi(string $lokasi): ?string
    {
        [$lat, $lng] = array_pad(explode(',', $lokasi), 2, '');

        try {
            $res = Http::timeout(5)->withUserAgent(config('app.name') . ' perbaikan')
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2', 'accept-language' => 'id', 'lat' => trim($lat), 'lon' => trim($lng),
                ]);

            return $res->ok() ? mb_substr((string) $res->json('display_name'), 0, 255) ?: null : null;
        } catch (\Throwable) {
            return null;
        }
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
