<?php

namespace App\Http\Controllers\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\MasterKolektor;
use App\Support\AdminDesaScope;
use Illuminate\Http\Request;
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

        $query = Tagihan::with(['pelanggan', 'kolektor']);

        if (auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin')) {
            $query->where('kolektor_id', auth()->user()->kolektor_id);
        } elseif (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyTagihanScope($query);
        }

        $query->when($bulan, fn ($q) => $q->where('bulan', $bulan))
              ->when($tahun, fn ($q) => $q->where('tahun', $tahun))
              ->when($request->kolektor_id, fn ($q, $v) => $q->where('kolektor_id', $v));

        $statsQuery = clone $query;

        $totalOmzet = (clone $statsQuery)->sum('terbayar');
        $totalPiutang = (clone $statsQuery)->sum(DB::raw('GREATEST(nominal - terbayar, 0)'));
        $totalLunas = (clone $statsQuery)->where('status', 'lunas')->count();

        $rekap = $query->latest()->paginate(50)->withQueryString();

        $kolektor = auth()->user()->hasRole('superadmin')
            ? MasterKolektor::all()
            : MasterKolektor::where('id', auth()->user()->kolektor_id)->get();

        return view('tagihan.rekap', compact(
            'rekap',
            'kolektor',
            'bulan',
            'tahun',
            'totalOmzet',
            'totalPiutang',
            'totalLunas'
        ));
    }

    public function export(Request $request)
    {
        // Implementation for Excel export would go here using maatwebsite/excel
        return back()->with('success', 'Fitur ekspor sedang disiapkan.');
    }
}
