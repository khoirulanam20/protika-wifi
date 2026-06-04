<?php

namespace App\Support;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;

class AdminDesaScope
{
    public static function isAdminDesaOnly(): bool
    {
        $user = auth()->user();

        return $user
            && $user->hasRole('admin_desa')
            && !$user->hasRole('superadmin');
    }

    public static function desaKode(): ?string
    {
        return auth()->user()?->adminDesa?->desa_kode;
    }

    public static function wilayahLabels(): ?array
    {
        $admin = auth()->user()?->adminDesa;

        if (!$admin) {
            return null;
        }

        return [
            'kecamatan' => $admin->kecamatan,
            'desa' => $admin->desa,
            'desa_kode' => $admin->desa_kode,
        ];
    }

    public static function applyPelangganScope(Builder $query): Builder
    {
        $desaKode = self::desaKode();

        if (!$desaKode) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($desaKode) {
            $q->where('desa_kode', $desaKode)
                ->orWhereHas('dusun', fn ($d) => $d->where('desa_kode', $desaKode));
        });
    }

    public static function applyTagihanScope(Builder $query): Builder
    {
        return $query->whereHas('pelanggan', function ($q) {
            self::applyPelangganScope($q);
        });
    }

    public static function applyPelangganScopeByDesaKode(Builder $query, string $desaKode): Builder
    {
        return $query->where(function ($q) use ($desaKode) {
            $q->where('desa_kode', $desaKode)
                ->orWhereHas('dusun', fn ($d) => $d->where('desa_kode', $desaKode));
        });
    }

    public static function applyTagihanScopeByDesaKode(Builder $query, string $desaKode): Builder
    {
        return $query->whereHas('pelanggan', function ($q) use ($desaKode) {
            self::applyPelangganScopeByDesaKode($q, $desaKode);
        });
    }

    public static function applyDusunScope(Builder $query): Builder
    {
        $desaKode = self::desaKode();

        if (!$desaKode) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('desa_kode', $desaKode);
    }

    public static function applyWilayahMasterScope(Builder $query): Builder
    {
        $labels = self::wilayahLabels();

        if (!$labels || (!$labels['kecamatan'] && !$labels['desa'])) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->when($labels['kecamatan'], fn ($q, $v) => $q->where('kecamatan', $v))
            ->when($labels['desa'], fn ($q, $v) => $q->where('desa', $v));
    }

    public static function pelangganInScope($pelanggan): bool
    {
        if (!self::isAdminDesaOnly()) {
            return true;
        }

        $desaKode = self::desaKode();

        if (!$desaKode) {
            return false;
        }

        if ($pelanggan->desa_kode === $desaKode) {
            return true;
        }

        if ($pelanggan->relationLoaded('dusun')) {
            return $pelanggan->dusun?->desa_kode === $desaKode;
        }

        return $pelanggan->dusun()->where('desa_kode', $desaKode)->exists();
    }

    public static function resolveDesaFromKode(string $desaKode): array
    {
        $desa = Wilayah::find($desaKode);
        $kecamatanKode = $desa ? substr($desaKode, 0, 8) : null;
        $kecamatan = $kecamatanKode ? Wilayah::find($kecamatanKode) : null;

        return [
            'desa' => $desa?->nama,
            'kecamatan' => $kecamatan?->nama,
        ];
    }

    public static function wilayahDisplay(): ?array
    {
        $admin = auth()->user()?->adminDesa;

        if (!$admin || !$admin->desa_kode) {
            return null;
        }

        $desaKode = $admin->desa_kode;
        $resolved = self::resolveDesaFromKode($desaKode);

        $provinsiKode = substr($desaKode, 0, 2);
        $provinsi = Wilayah::find($provinsiKode);

        return [
            'provinsi' => $provinsi?->nama,
            'kecamatan' => $admin->kecamatan ?: $resolved['kecamatan'],
            'desa' => $admin->desa ?: $resolved['desa'],
            'desa_kode' => $desaKode,
        ];
    }

    public static function applyWilayahToData(array $data): array
    {
        $labels = self::wilayahLabels();

        if ($labels) {
            $data['kecamatan'] = $labels['kecamatan'];
            $data['desa'] = $labels['desa'];
            $data['desa_kode'] = $labels['desa_kode'];
        }

        return $data;
    }

    public static function authorizeWilayahRecord(object $record): void
    {
        if (!self::isAdminDesaOnly()) {
            return;
        }

        $labels = self::wilayahLabels();

        if (!$labels) {
            abort(403);
        }

        if ($record->kecamatan !== $labels['kecamatan'] || $record->desa !== $labels['desa']) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }
}
