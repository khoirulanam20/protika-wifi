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
            $dusun = $a['hamlet'] ?? $a['neighbourhood'] ?? $a['quarter'] ?? null;
            $desa = $a['village'] ?? $a['suburb'] ?? null;
            $kec = $a['municipality'] ?? $a['city_district'] ?? $a['district'] ?? $a['subdistrict'] ?? null;
            $kab = $a['county'] ?? $a['city'] ?? $a['regency'] ?? null;

            // OSM sering tidak punya tag kecamatan di zoom detail; zoom 12 mengembalikan batas kecamatan.
            if (! $kec) {
                usleep(1100000); // batas Nominatim: 1 request/detik
                try {
                    $z = Http::timeout(5)->withUserAgent(config('app.name') . ' perbaikan')
                        ->get('https://nominatim.openstreetmap.org/reverse', [
                            'format' => 'jsonv2', 'accept-language' => 'id', 'addressdetails' => 1, 'zoom' => 12,
                            'lat' => $lat, 'lon' => $lng,
                        ])->json('address') ?? [];
                    $kec = $z['town'] ?? $z['municipality'] ?? $z['city_district'] ?? $z['district'] ?? $z['subdistrict'] ?? null;
                } catch (\Throwable) {
                }
            }

            // Cadangan: cari dari tabel wilayah lewat desa di dalam kabupaten.
            if (! $kec && $desa && $kab) {
                $prov = Wilayah::whereRaw('LENGTH(kode) = 2')->where('nama', $a['state'] ?? '')->value('kode');
                $kabKode = Wilayah::whereRaw('LENGTH(kode) = 5')->whereIn('nama', ["Kabupaten $kab", "Kota $kab", $kab])
                    ->when($prov, fn ($q) => $q->where('kode', 'like', "$prov.%"))->value('kode');
                $kodeDesa = $kabKode ? Wilayah::whereRaw('LENGTH(kode) = 13')->where('kode', 'like', "$kabKode.%")->where('nama', $desa)->pluck('kode') : collect();
                // desa bernama sama di beberapa kecamatan = ambigu, kecamatan dikosongkan
                if ($kodeDesa->count() === 1) {
                    $kec = Wilayah::where('kode', substr($kodeDesa[0], 0, 8))->value('nama');
                }
            }

            $parts = array_filter([$dusun, $desa, $kec, $kab]);

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
