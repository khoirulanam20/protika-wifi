<?php

namespace App\Http\Controllers\Tagihan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\MasterKolektor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with(['pelanggan', 'kolektor']);

        $query->when($request->bulan,  fn($q, $v) => $q->where('bulan', $v))
              ->when($request->tahun,  fn($q, $v) => $q->where('tahun', $v))
              ->when($request->kolektor_id, fn($q, $v) => $q->where('kolektor_id', $v));

        $rekap = $query->latest()->paginate(50);
        $kolektor = MasterKolektor::all();
        
        $totalNominal = $query->sum('nominal');
        $totalLunas = (clone $query)->where('status', 'lunas')->count();

        return view('tagihan.rekap', compact('rekap', 'kolektor', 'totalNominal', 'totalLunas'));
    }

    public function export(Request $request)
    {
        // Implementation for Excel export would go here using maatwebsite/excel
        return back()->with('success', 'Fitur ekspor sedang disiapkan.');
    }
}
