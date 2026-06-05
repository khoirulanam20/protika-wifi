@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Ringkasan data operasional Protika WiFi')

@section('content')

    {{-- Filter --}}
    <form method="GET" action="{{ route('dashboard') }}" class="card p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-content-secondary text-xs font-medium mb-1.5">Bulan</label>
            <select name="bulan" class="input-field w-40">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ (int) $bulan === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-content-secondary text-xs font-medium mb-1.5">Tahun</label>
            <select name="tahun" class="input-field w-32">
                @foreach(range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" {{ (int) $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        @if($isSuperadmin)
        <div>
            <label class="block text-content-secondary text-xs font-medium mb-1.5">Admin Desa</label>
            <select name="admin_desa_id" class="input-field w-56">
                <option value="">Semua Admin Desa</option>
                @foreach($adminDesaList as $ad)
                    <option value="{{ $ad->id }}" {{ (int) ($adminDesaId ?? 0) === $ad->id ? 'selected' : '' }}>
                        {{ $ad->nama_admin }}@if($ad->desa) — {{ $ad->desa }}@endif
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-content-secondary text-xs font-medium mb-1.5">Kolektor</label>
            <select name="kolektor_id" class="input-field w-48">
                <option value="">Semua Kolektor</option>
                @foreach($kolektorList as $k)
                    <option value="{{ $k->id }}" {{ (int) ($kolektorId ?? 0) === $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kolektor }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['bulan', 'tahun', 'kolektor_id', 'admin_desa_id']))
            <a href="{{ route('dashboard') }}" class="btn-secondary">Reset</a>
        @endif
    </form>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Pelanggan --}}
        <div class="card p-6 flex flex-col justify-between items-start animate-fade-in-up group" style="animation-delay: 0.1s;">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-light to-primary/20 text-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-stat">{{ $stats['total_pelanggan'] }}</p>
                <p class="text-label mt-1">Total Pelanggan</p>
            </div>
        </div>

        {{-- Lunas --}}
        <div class="card p-6 flex flex-col justify-between items-start animate-fade-in-up group" style="animation-delay: 0.2s;">
            <div
                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-status-success/20 to-status-success/30 text-status-success flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-stat">{{ $stats['lunas'] }}</p>
                <p class="text-label mt-1">Lunas · {{ $periodeLabel }}</p>
            </div>
        </div>

        {{-- Belum Lunas --}}
        <div class="card p-6 flex flex-col justify-between items-start animate-fade-in-up group" style="animation-delay: 0.3s;">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-status-danger/20 to-status-danger/30 text-status-danger flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-stat">{{ $stats['belum_lunas'] }}</p>
                <p class="text-label mt-1">Belum Lunas · {{ $periodeLabel }}</p>
            </div>
        </div>

        {{-- Pendapatan --}}
        <div class="card p-6 flex flex-col justify-between items-start animate-fade-in-up group" style="animation-delay: 0.4s;">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-500/30 text-amber-500 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[32px] md:text-stat font-extrabold text-content-primary tracking-tight">Rp
                    {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
                <p class="text-label mt-1">Pendapatan Lunas · {{ $periodeLabel }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Area: Chart & Recent --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Revenue Chart --}}
            <div class="card p-6 animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-title">Tren Pendapatan</h3>
                        <p class="text-label">6 bulan hingga {{ $trenEndLabel }} · tagihan lunas</p>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-xs text-primary font-bold tracking-wide uppercase">Lunas</span>
                    </div>
                </div>
                <div class="relative w-full h-[320px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            @if($isSuperadmin)
            {{-- Revenue per Kolektor (stacked) --}}
            <div class="card p-6 animate-fade-in-up" style="animation-delay: 0.55s;">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-title">Tren Pendapatan per Kolektor</h3>
                        <p class="text-label">6 bulan hingga {{ $trenEndLabel }}, per kolektor</p>
                    </div>
                </div>
                <div class="relative w-full h-[360px]">
                    <canvas id="revenuePerKolektorChart"></canvas>
                </div>
            </div>

            {{-- Pelanggan per Kolektor --}}
            <div class="card p-6 animate-fade-in-up" style="animation-delay: 0.58s;">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-title">Jumlah Pelanggan per Kolektor</h3>
                        <p class="text-label">Total pelanggan aktif terdaftar per kolektor</p>
                    </div>
                </div>
                @php
                    $pelangganLabelCount = count($pelangganPerKolektor['labels'] ?? []);
                    $pelangganChartInnerHeight = max(280, $pelangganLabelCount * 28);
                @endphp
                <div class="relative w-full max-h-[420px] overflow-y-auto">
                    <div class="relative w-full" style="height: {{ $pelangganChartInnerHeight }}px">
                        <canvas id="pelangganPerKolektorChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Side Area: Stats & Info --}}
        <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.7s;">
            {{-- Progress Card --}}
            <div class="card-dark p-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-400/20 rounded-full blur-3xl group-hover:bg-blue-400/30 transition-all duration-700"></div>
                
                <div class="absolute top-0 right-0 p-4 opacity-[0.03] transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <h4 class="text-xl font-bold mb-8 flex items-center gap-2">
                        Efisiensi Penagihan
                        <span class="flex h-3 w-3 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-40"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-white/80"></span>
                        </span>
                    </h4>

                    @php 
                        $percent = $stats['tagihan_bulan'] > 0 ? round(($stats['lunas'] / $stats['tagihan_bulan']) * 100) : 0;
                    @endphp

                    <div class="flex items-center justify-center mb-10">
                        <div class="relative flex items-center justify-center">
                            {{-- Drop Shadow for circular chart --}}
                            <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-xl"></div>
                            
                            <svg class="w-40 h-40 transform -rotate-90 drop-shadow-2xl">
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-white/10" stroke-linecap="round" />
                                <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="439.8" stroke-dashoffset="{{ 439.8 - (439.8 * $percent / 100) }}" class="text-white transition-all duration-1500 ease-out" stroke-linecap="round" />
                            </svg>
                            <div class="absolute flex flex-col items-center">
                                <span class="text-4xl font-black tracking-tighter animate-float">{{ $percent }}%</span>
                                <span class="text-[10px] text-white/80 uppercase tracking-[0.2em] font-bold mt-1">Lunas</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-white/10 border border-white/20 backdrop-blur-sm hover:bg-white/15 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/10 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <span class="text-sm font-medium text-white/90">Total Target</span>
                            </div>
                            <span class="text-lg font-bold">{{ $stats['tagihan_bulan'] }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-white/10 border border-white/20 backdrop-blur-sm hover:bg-white/15 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/10 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-sm font-medium text-white/90">Terselesaikan</span>
                            </div>
                            <span class="text-lg font-bold">{{ $stats['lunas'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Table --}}
            <div class="card overflow-hidden animate-fade-in-up" style="animation-delay: 0.65s;">
                <div class="px-5 py-4 border-b border-border flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-white/50 backdrop-blur-sm">
                    <div>
                        <h3 class="text-base font-bold text-content-primary">Tagihan Pending Terbaru</h3>
                        <p class="text-xs text-content-secondary mt-0.5">Daftar pelanggan yang belum lunas</p>
                    </div>
                    <a href="{{ route('tagihan.index', ['status' => 'belum_lunas', 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                        class="px-3 py-2 bg-base-input hover:bg-border rounded-lg text-xs font-semibold text-primary transition-colors flex items-center justify-center gap-1.5 shrink-0">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-content-tertiary text-left bg-base-input/50">
                                <th class="px-4 py-3 font-semibold">Pelanggan</th>
                                <th class="px-4 py-3 font-semibold">Periode</th>
                                <th class="px-4 py-3 font-semibold text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/50 bg-white/40">
                            @forelse($pending as $item)
                                <tr class="hover:bg-primary/5 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs group-hover:bg-primary group-hover:text-white transition-colors shrink-0">
                                                {{ substr($item->pelanggan->nama_pelanggan, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-semibold text-content-primary block truncate text-xs">{{ $item->pelanggan->nama_pelanggan }}</span>
                                                <span class="text-[10px] text-content-tertiary truncate block">{{ $item->pelanggan->desa }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white border border-border text-content-secondary text-[10px] font-medium">
                                            {{ date('M', mktime(0, 0, 0, $item->bulan, 1)) }} {{ $item->tahun }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-content-primary text-xs whitespace-nowrap">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center">
                                        <div class="flex flex-col items-center justify-center text-content-tertiary">
                                            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                            <p class="text-sm font-medium">Tidak ada tagihan pending.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Links / Help --}}
            <div class="card p-6 border-l-4 border-l-primary">
                <h4 class="text-sm font-bold text-primary uppercase tracking-widest mb-4">Pusat Bantuan</h4>
                <div class="space-y-3">
                    <a href="#" class="flex items-center gap-4 p-3 rounded-xl bg-base-input hover:bg-primary/5 border border-transparent hover:border-primary/20 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-content-primary block mb-0.5 group-hover:text-primary transition-colors">Panduan Pengguna</span>
                            <span class="text-xs text-content-tertiary">Pelajari cara menggunakan sistem</span>
                        </div>
                    </a>

                    <a href="#" class="flex items-center gap-4 p-3 rounded-xl bg-base-input hover:bg-status-success/5 border border-transparent hover:border-status-success/20 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-status-success flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-content-primary block mb-0.5 group-hover:text-status-success transition-colors">Hubungi Support</span>
                            <span class="text-xs text-content-tertiary">Butuh bantuan lebih lanjut?</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rupiahTick = (val) => {
                    if (val >= 1000000) {
                        return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                    }
                    return 'Rp ' + val.toLocaleString('id-ID');
                };

                const tooltipScopeLabel = @json($tooltipScopeLabel);

                const revenueEl = document.getElementById('revenueChart');
                if (revenueEl) {
                    const ctx = revenueEl.getContext('2d');
                    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(37, 99, 235, 1)');
                    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.4)');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json(collect($tren)->pluck('bulan')),
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: @json(collect($tren)->pluck('total')),
                                backgroundColor: gradient,
                                borderRadius: 8,
                                barThickness: 32,
                                hoverBackgroundColor: '#1D4ED8',
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 2000, easing: 'easeOutQuart' },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                                    bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: (ctx) => 'Rp ' + ctx.raw.toLocaleString('id-ID'),
                                        footer: () => tooltipScopeLabel || '',
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    border: { display: false },
                                    grid: { color: '#F1F5F9', drawTicks: false },
                                    ticks: { padding: 10, callback: rupiahTick, color: '#94A3B8', font: { size: 11, family: "'Inter', sans-serif", weight: '500' } }
                                },
                                x: {
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: { padding: 10, color: '#64748B', font: { size: 12, family: "'Inter', sans-serif", weight: '600' } }
                                }
                            }
                        }
                    });
                }

                @if($isSuperadmin)
                const kolektorColors = [
                    '#2563EB', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                    '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
                ];

                const trenPerKolektor = @json($trenPerKolektor);
                const perKolektorEl = document.getElementById('revenuePerKolektorChart');
                if (perKolektorEl && trenPerKolektor.datasets.length) {
                    const stackedDatasets = trenPerKolektor.datasets.map((ds, i) => ({
                        label: ds.label,
                        data: ds.data,
                        backgroundColor: kolektorColors[i % kolektorColors.length],
                        borderRadius: 4,
                        borderWidth: 0,
                        stack: 'stack0',
                    }));

                    new Chart(perKolektorEl.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: trenPerKolektor.labels,
                            datasets: stackedDatasets,
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: { boxWidth: 12, padding: 16, font: { size: 11, family: "'Inter', sans-serif" } }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                                    bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: (ctx) => `${ctx.dataset.label}: Rp ${ctx.raw.toLocaleString('id-ID')}`,
                                    }
                                },
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: { color: '#64748B', font: { size: 12, family: "'Inter', sans-serif", weight: '600' } }
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    border: { display: false },
                                    grid: { color: '#F1F5F9', drawTicks: false },
                                    ticks: { padding: 10, callback: rupiahTick, color: '#94A3B8', font: { size: 11, family: "'Inter', sans-serif", weight: '500' } }
                                }
                            }
                        }
                    });
                }

                const pelangganPerKolektor = @json($pelangganPerKolektor);
                const pelangganEl = document.getElementById('pelangganPerKolektorChart');
                if (pelangganEl && pelangganPerKolektor.labels.length) {
                    const pelCtx = pelangganEl.getContext('2d');
                    const pelHeight = pelangganEl.parentElement?.clientHeight ?? 400;
                    let pelGradient = pelCtx.createLinearGradient(0, 0, 0, pelHeight);
                    pelGradient.addColorStop(0, 'rgba(16, 185, 129, 1)');
                    pelGradient.addColorStop(1, 'rgba(16, 185, 129, 0.5)');

                    new Chart(pelCtx, {
                        type: 'bar',
                        data: {
                            labels: pelangganPerKolektor.labels,
                            datasets: [{
                                label: 'Jumlah Pelanggan',
                                data: pelangganPerKolektor.counts,
                                backgroundColor: pelGradient,
                                borderRadius: 8,
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: (ctx) => `${ctx.label}: ${ctx.raw} pelanggan`,
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    border: { display: false },
                                    grid: { color: '#F1F5F9', drawTicks: false },
                                    ticks: { stepSize: 1, color: '#94A3B8', font: { size: 11, family: "'Inter', sans-serif" } }
                                },
                                y: {
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: {
                                        autoSkip: false,
                                        color: '#64748B',
                                        font: { size: 11, family: "'Inter', sans-serif", weight: '600' },
                                    }
                                }
                            }
                        }
                    });
                }
                @endif
            });
        </script>
    @endpush

@endsection
