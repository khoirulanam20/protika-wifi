<?php

namespace App\Http\Controllers;

use App\Models\MasterAdminDesa;
use App\Models\MasterKolektor;
use App\Models\MasterPelanggan;
use App\Models\Tagihan;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = (int) $request->input('tahun', now()->year);
        $tahun = max(now()->year - 5, min(now()->year, $tahun));

        $kolektorId = null;
        $adminDesaId = null;
        $adminDesa = null;

        if ($isSuperadmin) {
            if ($request->filled('kolektor_id')) {
                $id = (int) $request->kolektor_id;
                if (MasterKolektor::where('id', $id)->exists()) {
                    $kolektorId = $id;
                }
            }

            if ($request->filled('admin_desa_id')) {
                $id = (int) $request->admin_desa_id;
                $adminDesa = MasterAdminDesa::find($id);
                if ($adminDesa) {
                    $adminDesaId = $adminDesa->id;
                }
            }
        }

        $stats = $this->buildStats($bulan, $tahun, $kolektorId, $adminDesaId, $user, $isSuperadmin);
        $tren = $this->buildTren($bulan, $tahun, $kolektorId, $adminDesaId, $user, $isSuperadmin);
        $pending = $this->buildPending($bulan, $tahun, $kolektorId, $adminDesaId, $user, $isSuperadmin);
        $tooltipScopeLabel = $this->buildTooltipScopeLabel($kolektorId, $adminDesa, $user, $isSuperadmin);

        $trenPerKolektor = ['labels' => [], 'datasets' => []];
        $pelangganPerKolektor = ['labels' => [], 'counts' => []];
        $kolektorList = collect();
        $adminDesaList = collect();

        if ($isSuperadmin) {
            $trenPerKolektor = $this->buildTrenPerKolektor($bulan, $tahun, $kolektorId, $adminDesaId);
            $pelangganPerKolektor = $this->buildPelangganPerKolektor($kolektorId, $adminDesaId);
            $kolektorList = MasterKolektor::orderBy('nama_kolektor')->get();
            $adminDesaList = MasterAdminDesa::orderBy('nama_admin')->get();
        }

        $periodeLabel = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
        $trenEndLabel = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('M Y');

        $activeFilterKeys = ['kolektor_id', 'admin_desa_id'];
        if ($request->has('bulan') && (int) $request->bulan !== now()->month) {
            $activeFilterKeys[] = 'bulan';
        }
        if ($request->has('tahun') && (int) $request->tahun !== now()->year) {
            $activeFilterKeys[] = 'tahun';
        }
        $activeFilterCount = WilayahFilter::countActiveFilters($request, $activeFilterKeys);

        return view('dashboard', compact(
            'stats',
            'tren',
            'pending',
            'isSuperadmin',
            'trenPerKolektor',
            'pelangganPerKolektor',
            'bulan',
            'tahun',
            'kolektorList',
            'kolektorId',
            'adminDesaList',
            'adminDesaId',
            'periodeLabel',
            'trenEndLabel',
            'tooltipScopeLabel',
            'activeFilterCount',
        ));
    }

    private function desaKodeForAdminDesa(?int $adminDesaId): ?string
    {
        if (!$adminDesaId) {
            return null;
        }

        return MasterAdminDesa::find($adminDesaId)?->desa_kode;
    }

    private function tagihanQuery($user, bool $isSuperadmin): Builder
    {
        $query = Tagihan::query();

        if ($isSuperadmin) {
            return $query;
        }

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyTagihanScope($query);

            return $query;
        }

        return $query->where('kolektor_id', $user->kolektor_id);
    }

    private function pelangganQuery($user, bool $isSuperadmin): Builder
    {
        $query = MasterPelanggan::query();

        if ($isSuperadmin) {
            return $query;
        }

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyPelangganScope($query);

            return $query;
        }

        return $query->where('kolektor_id', $user->kolektor_id);
    }

    private function applySuperadminFilters(
        Builder $query,
        ?int $kolektorId,
        ?int $adminDesaId,
        bool $isSuperadmin,
        string $type
    ): Builder {
        if (!$isSuperadmin) {
            return $query;
        }

        if ($kolektorId && $type === 'tagihan') {
            $query->where('kolektor_id', $kolektorId);
        }

        if ($kolektorId && $type === 'pelanggan') {
            $query->where('kolektor_id', $kolektorId);
        }

        $desaKode = $this->desaKodeForAdminDesa($adminDesaId);
        if ($desaKode) {
            if ($type === 'pelanggan') {
                AdminDesaScope::applyPelangganScopeByDesaKode($query, $desaKode);
            } else {
                AdminDesaScope::applyTagihanScopeByDesaKode($query, $desaKode);
            }
        } elseif ($adminDesaId) {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function buildStats(int $bulan, int $tahun, ?int $kolektorId, ?int $adminDesaId, $user, bool $isSuperadmin): array
    {
        $pelangganQ = $this->pelangganQuery($user, $isSuperadmin);
        $this->applySuperadminFilters($pelangganQ, $kolektorId, $adminDesaId, $isSuperadmin, 'pelanggan');

        $tagihanQ = $this->tagihanQuery($user, $isSuperadmin);
        $this->applySuperadminFilters($tagihanQ, $kolektorId, $adminDesaId, $isSuperadmin, 'tagihan');

        $periodTagihan = (clone $tagihanQ)->where('bulan', $bulan)->where('tahun', $tahun);

        return [
            'total_pelanggan' => (clone $pelangganQ)->count(),
            'tagihan_bulan'   => (clone $periodTagihan)->count(),
            'lunas'           => (clone $periodTagihan)->where('status', 'lunas')->count(),
            'belum_lunas'     => (clone $periodTagihan)->where('status', 'belum_lunas')->count(),
            'total_nominal'   => (int) (clone $periodTagihan)->where('status', 'lunas')->sum('nominal'),
        ];
    }

    private function buildTren(int $endBulan, int $endTahun, ?int $kolektorId, ?int $adminDesaId, $user, bool $isSuperadmin): array
    {
        $tren = [];
        foreach ($this->buildMonthPeriods($endBulan, $endTahun) as $period) {
            $q = $this->tagihanQuery($user, $isSuperadmin);
            $this->applySuperadminFilters($q, $kolektorId, $adminDesaId, $isSuperadmin, 'tagihan');

            $val = (clone $q)
                ->where('bulan', $period['bulan'])
                ->where('tahun', $period['tahun'])
                ->where('status', 'lunas')
                ->sum('nominal');

            $tren[] = [
                'bulan' => $period['label'],
                'total' => (int) $val,
            ];
        }

        return $tren;
    }

    private function buildPending(int $bulan, int $tahun, ?int $kolektorId, ?int $adminDesaId, $user, bool $isSuperadmin)
    {
        if (!$isSuperadmin && !AdminDesaScope::isAdminDesaOnly()) {
            return collect();
        }

        $q = $this->tagihanQuery($user, $isSuperadmin);
        $this->applySuperadminFilters($q, $kolektorId, $adminDesaId, $isSuperadmin, 'tagihan');

        return $q->with('pelanggan')
            ->where('status', 'belum_lunas')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->latest()
            ->take(5)
            ->get();
    }

    private function buildTooltipScopeLabel(?int $kolektorId, ?MasterAdminDesa $adminDesa, $user, bool $isSuperadmin): string
    {
        if ($isSuperadmin) {
            $parts = [];

            if ($adminDesa) {
                $desaLabel = trim(($adminDesa->desa ?? '') . ($adminDesa->kecamatan ? ', ' . $adminDesa->kecamatan : ''));
                $parts[] = 'Desa: ' . ($desaLabel ?: $adminDesa->nama_admin);
            }

            if ($kolektorId) {
                $parts[] = 'Kolektor: ' . (MasterKolektor::find($kolektorId)?->nama_kolektor ?? '—');
            }

            if ($parts) {
                return implode(' · ', $parts);
            }

            return 'Semua wilayah';
        }

        if (AdminDesaScope::isAdminDesaOnly()) {
            $wilayah = AdminDesaScope::wilayahDisplay();

            if ($wilayah) {
                return 'Desa: ' . trim(($wilayah['desa'] ?? '') . ', ' . ($wilayah['kecamatan'] ?? ''), ', ');
            }

            return 'Desa admin';
        }

        $nama = $user->kolektor?->nama_kolektor;

        return $nama ? 'Kolektor: ' . $nama : '';
    }

    private function buildMonthPeriods(int $endBulan, int $endTahun): array
    {
        $end = Carbon::createFromDate($endTahun, $endBulan, 1)->endOfMonth();
        $periods = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $end->copy()->subMonths($i);
            $periods[] = [
                'bulan' => $date->month,
                'tahun' => $date->year,
                'label' => $date->format('M'),
            ];
        }

        return $periods;
    }

    private function applyAdminDesaToTagihan(Builder $query, ?int $adminDesaId): Builder
    {
        $desaKode = $this->desaKodeForAdminDesa($adminDesaId);

        if (!$adminDesaId) {
            return $query;
        }

        if (!$desaKode) {
            return $query->whereRaw('1 = 0');
        }

        return AdminDesaScope::applyTagihanScopeByDesaKode($query, $desaKode);
    }

    private function buildTrenPerKolektor(int $endBulan, int $endTahun, ?int $kolektorId, ?int $adminDesaId): array
    {
        $monthPeriods = $this->buildMonthPeriods($endBulan, $endTahun);

        $aggregatesQuery = Tagihan::query()
            ->where('status', 'lunas')
            ->whereNotNull('kolektor_id')
            ->when($kolektorId, fn ($q) => $q->where('kolektor_id', $kolektorId));

        $this->applyAdminDesaToTagihan($aggregatesQuery, $adminDesaId);

        $aggregates = $aggregatesQuery
            ->where(function ($q) use ($monthPeriods) {
                foreach ($monthPeriods as $period) {
                    $q->orWhere(function ($sub) use ($period) {
                        $sub->where('bulan', $period['bulan'])
                            ->where('tahun', $period['tahun']);
                    });
                }
            })
            ->selectRaw('kolektor_id, bulan, tahun, SUM(nominal) as total')
            ->groupBy('kolektor_id', 'bulan', 'tahun')
            ->get();

        $lookup = [];
        foreach ($aggregates as $row) {
            $lookup["{$row->kolektor_id}-{$row->bulan}-{$row->tahun}"] = (int) $row->total;
        }

        $kolektors = MasterKolektor::query()
            ->when($kolektorId, fn ($q) => $q->where('id', $kolektorId))
            ->orderBy('nama_kolektor')
            ->get();

        $datasets = [];
        foreach ($kolektors as $kolektor) {
            $data = [];
            foreach ($monthPeriods as $period) {
                $key = "{$kolektor->id}-{$period['bulan']}-{$period['tahun']}";
                $data[] = $lookup[$key] ?? 0;
            }
            $datasets[] = [
                'label' => $kolektor->nama_kolektor,
                'data' => $data,
            ];
        }

        return [
            'labels' => array_column($monthPeriods, 'label'),
            'datasets' => $datasets,
        ];
    }

    private function buildPelangganPerKolektor(?int $kolektorId, ?int $adminDesaId): array
    {
        $desaKode = $this->desaKodeForAdminDesa($adminDesaId);

        $rows = MasterKolektor::query()
            ->when($kolektorId, fn ($q) => $q->where('id', $kolektorId))
            ->withCount([
                'pelanggan' => function ($q) use ($desaKode, $adminDesaId) {
                    if ($adminDesaId && $desaKode) {
                        AdminDesaScope::applyPelangganScopeByDesaKode($q, $desaKode);
                    } elseif ($adminDesaId) {
                        $q->whereRaw('1 = 0');
                    }
                },
            ])
            ->orderBy('nama_kolektor')
            ->get();

        return [
            'labels' => $rows->pluck('nama_kolektor')->values()->all(),
            'counts' => $rows->pluck('pelanggan_count')->values()->all(),
        ];
    }
}
