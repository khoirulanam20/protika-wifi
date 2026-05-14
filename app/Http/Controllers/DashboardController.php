<?php

namespace App\Http\Controllers;

use App\Models\MasterPelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->hasRole('superadmin');

        if ($isSuperadmin) {
            $stats = [
                'total_pelanggan' => MasterPelanggan::count(),
                'tagihan_bulan'   => Tagihan::where('bulan', now()->month)->where('tahun', now()->year)->count(),
                'lunas'           => Tagihan::where('bulan', now()->month)->where('tahun', now()->year)->where('status', 'lunas')->count(),
                'belum_lunas'     => Tagihan::where('bulan', now()->month)->where('tahun', now()->year)->where('status', 'belum_lunas')->count(),
                'total_nominal'   => Tagihan::where('bulan', now()->month)->where('tahun', now()->year)->where('status', 'lunas')->sum('nominal'),
            ];

            // Tren pendapatan 6 bulan terakhir
            $tren = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $m = $date->month;
                $y = $date->year;
                $val = Tagihan::where('bulan', $m)->where('tahun', $y)->where('status', 'lunas')->sum('nominal');
                $tren[] = [
                    'bulan' => $date->format('M'),
                    'total' => (int)$val
                ];
            }

            $pending = Tagihan::with('pelanggan')->where('status', 'belum_lunas')
                              ->latest()->take(5)->get();
            return view('dashboard', compact('stats', 'tren', 'pending', 'isSuperadmin'));
        }

        // Kolektor Dashboard
        $kolektorId = $user->kolektor_id;
        $stats = [
            'total_pelanggan' => MasterPelanggan::where('kolektor_id', $kolektorId)->count(),
            'tagihan_bulan'   => Tagihan::where('kolektor_id', $kolektorId)->where('bulan', now()->month)->where('tahun', now()->year)->count(),
            'lunas'           => Tagihan::where('kolektor_id', $kolektorId)
                                        ->where('bulan', now()->month)->where('tahun', now()->year)->where('status', 'lunas')->count(),
            'belum_lunas'     => Tagihan::where('kolektor_id', $kolektorId)
                                        ->where('bulan', now()->month)->where('tahun', now()->year)->where('status', 'belum_lunas')->count(),
            'total_nominal'   => Tagihan::where('kolektor_id', $kolektorId)
                                        ->whereDate('tanggal_bayar', today())->sum('nominal'),
        ];
        $tren = [];
        $pending = collect();
        return view('dashboard', compact('stats', 'tren', 'pending', 'isSuperadmin'));
    }
}
