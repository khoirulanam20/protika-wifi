<?php

namespace App\Http\Controllers\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\MasterKolektor;
use App\Models\MasterPelanggan;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->has('bulan')
            ? ($request->filled('bulan') ? (int) $request->bulan : null)
            : now()->month;

        $tahun = $request->has('tahun')
            ? ($request->filled('tahun') ? (int) $request->tahun : null)
            : now()->year;

        $statusFilter = $request->filled('status') ? $request->status : null;
        $isKolektorOnly = auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin');
        $isAdminDesaOnly = AdminDesaScope::isAdminDesaOnly();

        $currentQuery = $this->baseRekapQuery($request, $isKolektorOnly, $isAdminDesaOnly);
        $currentQuery->when($bulan, fn ($q) => $q->where('bulan', $bulan))
            ->when($tahun, fn ($q) => $q->where('tahun', $tahun));

        if ($statusFilter && $statusFilter !== 'pelunasan_bulan_sebelumnya') {
            $currentQuery->where('status', $statusFilter);
        }

        $statsQuery = clone $currentQuery;
        $totalOmzet = (clone $statsQuery)->sum('terbayar');
        $totalPiutang = (clone $statsQuery)->sum(DB::raw('GREATEST(nominal - terbayar, 0)'));
        $totalLunas = (clone $statsQuery)->where('status', 'lunas')->count();

        $pelunasanQuery = $this->baseRekapQuery($request, $isKolektorOnly, $isAdminDesaOnly);
        $this->applyPelunasanPrevScope($pelunasanQuery, $bulan, $tahun);

        $totalPelunasanPrev = (clone $pelunasanQuery)->sum('terbayar');

        if ($statusFilter === 'pelunasan_bulan_sebelumnya') {
            $merged = $pelunasanQuery->latest('tanggal_bayar')->get()
                ->each(fn ($item) => $item->setAttribute('is_pelunasan_prev', true));
        } elseif ($statusFilter) {
            $merged = $currentQuery->latest()->get();
        } else {
            $currentItems = $currentQuery->latest()->get();
            $prevItems = $pelunasanQuery->latest('tanggal_bayar')->get()
                ->each(fn ($item) => $item->setAttribute('is_pelunasan_prev', true));
            $merged = $currentItems->concat($prevItems)->sortByDesc(fn ($item) => $item->tanggal_bayar ?? $item->created_at)->values();
        }

        $perPage = 50;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $rekap = new LengthAwarePaginator(
            $merged->forPage($page, $perPage)->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($isKolektorOnly) {
            $kolektor = MasterKolektor::where('id', auth()->user()->kolektor_id)->get();
        } elseif ($isAdminDesaOnly) {
            $kolektorQuery = MasterKolektor::query();
            AdminDesaScope::applyWilayahMasterScope($kolektorQuery);
            $kolektor = $kolektorQuery->orderBy('nama_kolektor')->get();
        } else {
            $kolektor = MasterKolektor::orderBy('nama_kolektor')->get();
        }

        $scopeQuery = MasterPelanggan::query();

        if ($isKolektorOnly) {
            $scopeQuery->where('kolektor_id', auth()->user()->kolektor_id);
        } elseif ($isAdminDesaOnly) {
            AdminDesaScope::applyPelangganScope($scopeQuery);
        } elseif ($request->kolektor_id) {
            $scopeQuery->where('kolektor_id', $request->kolektor_id);
        }

        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery($scopeQuery, true, $request);
        $kecamatanList = $wilayahOptions['kecamatanList'];
        $desaOptions = $wilayahOptions['desaOptions'];
        $dusunOptions = $wilayahOptions['dusunOptions'];
        $activeFilterCount = WilayahFilter::countActiveFilters($request, [
            'kecamatan', 'desa', 'dusun_id', 'kolektor_id', 'status',
        ]);

        return view('tagihan.rekap', compact(
            'rekap',
            'kolektor',
            'bulan',
            'tahun',
            'totalOmzet',
            'totalPiutang',
            'totalLunas',
            'totalPelunasanPrev',
            'kecamatanList',
            'desaOptions',
            'dusunOptions',
            'activeFilterCount'
        ));
    }

    public function export(Request $request)
    {
        // Implementation for Excel export would go here using maatwebsite/excel
        return back()->with('success', 'Fitur ekspor sedang disiapkan.');
    }

    private function baseRekapQuery(Request $request, bool $isKolektorOnly, bool $isAdminDesaOnly)
    {
        $query = Tagihan::with(['pelanggan', 'kolektor']);

        if ($isKolektorOnly) {
            $query->where('kolektor_id', auth()->user()->kolektor_id);
        } elseif ($isAdminDesaOnly) {
            AdminDesaScope::applyTagihanScope($query);
        }

        if (!$isKolektorOnly) {
            $query->when($request->kolektor_id, fn ($q, $v) => $q->where('kolektor_id', $v));
        }

        WilayahFilter::applyViaPelanggan($query, $request);

        return $query;
    }

    private function applyPelunasanPrevScope($query, ?int $bulan, ?int $tahun): void
    {
        if (!$bulan || !$tahun) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where('status', 'lunas')
            ->whereNotNull('tanggal_bayar')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where(function ($q) use ($bulan, $tahun) {
                $q->where('tahun', '<', $tahun)
                    ->orWhere(function ($q2) use ($bulan, $tahun) {
                        $q2->where('tahun', $tahun)->where('bulan', '<', $bulan);
                    });
            });
    }
}
