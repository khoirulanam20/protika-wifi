<?php

namespace App\Support;

use App\Models\MasterDusun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WilayahFilter
{
    public static function applyDirectWilayah(Builder $query, Request $request): Builder
    {
        if (AdminDesaScope::isAdminDesaOnly()) {
            return $query->when($request->dusun_id, fn ($q, $v) => $q->where('id', $v));
        }

        return $query
            ->when($request->kecamatan, fn ($q, $v) => $q->where('kecamatan', $v))
            ->when($request->desa, fn ($q, $v) => $q->where('desa', $v))
            ->when($request->dusun_id, fn ($q, $v) => $q->where('id', $v));
    }

    public static function applyPelangganWilayah(Builder $query, Request $request): Builder
    {
        if (!AdminDesaScope::isAdminDesaOnly()) {
            $query
                ->when($request->kecamatan, fn ($q, $v) => $q->where('kecamatan', $v))
                ->when($request->desa, fn ($q, $v) => $q->where('desa', $v));
        }

        return $query->when($request->dusun_id, fn ($q, $v) => $q->where('dusun_id', $v));
    }

    public static function applyViaPelanggan(Builder $query, Request $request): Builder
    {
        if (!AdminDesaScope::isAdminDesaOnly()) {
            $query
                ->when($request->kecamatan, fn ($q, $v) => $q->whereHas(
                    'pelanggan',
                    fn ($p) => $p->where('kecamatan', $v)
                ))
                ->when($request->desa, fn ($q, $v) => $q->whereHas(
                    'pelanggan',
                    fn ($p) => $p->where('desa', $v)
                ));
        }

        return $query->when($request->dusun_id, fn ($q, $v) => $q->whereHas(
            'pelanggan',
            fn ($p) => $p->where('dusun_id', $v)
        ));
    }

    /**
     * @return array{kecamatanList: array<int, string>, desaOptions: \Illuminate\Support\Collection}
     */
    public static function buildKecamatanDesaOptions(Builder $scopedQuery): array
    {
        $kecamatanList = (clone $scopedQuery)
            ->select('kecamatan')
            ->whereNotNull('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan')
            ->filter()
            ->values()
            ->toArray();

        $desaOptions = (clone $scopedQuery)
            ->select('kecamatan', 'desa')
            ->whereNotNull('kecamatan')
            ->whereNotNull('desa')
            ->distinct()
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get()
            ->map(fn ($row) => [
                'kecamatan' => $row->kecamatan,
                'desa' => $row->desa,
            ])
            ->values();

        return compact('kecamatanList', 'desaOptions');
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{id: int, dusun: string, desa: string, kecamatan: string}>
     */
    public static function buildDusunOptions(?array $kecamatanList = null, bool $fromMasterDusun = true): \Illuminate\Support\Collection
    {
        $dusunOptionsQuery = MasterDusun::query()
            ->select('id', 'dusun', 'desa', 'kecamatan');

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyDusunScope($dusunOptionsQuery);
        } elseif (!empty($kecamatanList)) {
            $dusunOptionsQuery->whereIn('kecamatan', $kecamatanList);
        } else {
            $dusunOptionsQuery->whereRaw('1 = 0');
        }

        return $dusunOptionsQuery
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->orderBy('dusun')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'dusun' => $row->dusun,
                'desa' => $row->desa,
                'kecamatan' => $row->kecamatan,
            ])
            ->values();
    }

    /**
     * Build wilayah options from a direct master table query (kolektor, teknisi, etc.).
     *
     * @return array{kecamatanList: array<int, string>, desaOptions: \Illuminate\Support\Collection, dusunOptions: \Illuminate\Support\Collection}
     */
    public static function buildOptionsFromScopedQuery(Builder $scopedQuery, bool $includeDusun = false): array
    {
        $options = self::buildKecamatanDesaOptions($scopedQuery);
        $dusunOptions = $includeDusun
            ? self::buildDusunOptions($options['kecamatanList'])
            : collect();

        return array_merge($options, ['dusunOptions' => $dusunOptions]);
    }

    public static function countActiveFilters(Request $request, array $keys): int
    {
        $count = 0;

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $count++;
            }
        }

        return $count;
    }
}
