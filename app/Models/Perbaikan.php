<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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

    /** Dusun, desa, kecamatan, kabupaten dari koordinat "lat,lng" via Nominatim; null jika gagal. */
    public static function alamatDariLokasi(string $lokasi): ?string
    {
        [$lat, $lng] = array_pad(explode(',', $lokasi), 2, '');
        $lat = round((float) $lat, 5);
        $lng = round((float) $lng, 5);

        return Cache::remember("alamat:$lat,$lng", now()->addDay(), function () use ($lat, $lng) {
            try {
                $res = Http::timeout(5)->withUserAgent(config('app.name') . ' perbaikan')
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'jsonv2', 'accept-language' => 'id', 'addressdetails' => 1, 'zoom' => 18,
                        'lat' => $lat, 'lon' => $lng,
                    ]);
            } catch (\Throwable) {
                return null;
            }

            $a = $res->json('address') ?? [];
            // shortcut: key Nominatim untuk wilayah Indonesia bervariasi; urutan fallback ini hasil tebakan, sesuaikan jika ada daerah yang salah.
            $parts = array_filter([
                $a['hamlet'] ?? $a['neighbourhood'] ?? $a['quarter'] ?? null,
                $a['village'] ?? $a['suburb'] ?? null,
                $a['municipality'] ?? $a['city_district'] ?? $a['district'] ?? $a['subdistrict'] ?? null,
                $a['county'] ?? $a['city'] ?? $a['regency'] ?? null,
            ]);

            return $parts ? implode(', ', array_unique($parts)) : null;
        });
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
