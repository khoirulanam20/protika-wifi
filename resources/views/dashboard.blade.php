@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Ringkasan data operasional Protika WiFi')

@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Pelanggan --}}
    <div class="card p-6 flex flex-col justify-between items-start">
        <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-stat">{{ $stats['total_pelanggan'] }}</p>
            <p class="text-label mt-1">Total Pelanggan</p>
        </div>
    </div>

    {{-- Lunas --}}
    <div class="card p-6 flex flex-col justify-between items-start">
        <div class="w-8 h-8 rounded-full bg-status-success/20 text-status-success flex items-center justify-center mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-stat">{{ $stats['lunas'] }}</p>
            <p class="text-label mt-1">Lunas Bulan Ini</p>
        </div>
    </div>

    {{-- Belum Lunas --}}
    <div class="card p-6 flex flex-col justify-between items-start">
        <div class="w-8 h-8 rounded-full bg-status-danger/20 text-status-danger flex items-center justify-center mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-stat">{{ $stats['belum_lunas'] }}</p>
            <p class="text-label mt-1">Belum Lunas</p>
        </div>
    </div>

    {{-- Pendapatan --}}
    <div class="card p-6 flex flex-col justify-between items-start">
        <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-[32px] font-bold text-content-primary tracking-tight">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
            <p class="text-label mt-1">{{ $isSuperadmin ? 'Pendapatan Bulan Ini' : 'Setoran Hari Ini' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Main Area: Chart & Recent --}}
    <div class="lg:col-span-2 space-y-8">
        {{-- Revenue Chart --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-title">Tren Pendapatan</h3>
                    <p class="text-label">Grafik koleksi tagihan 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-xs text-content-secondary font-medium">Lunas</span>
                </div>
            </div>
            <div class="relative w-full h-[280px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Pending Table --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between bg-white">
                <h3 class="text-lg font-semibold text-content-primary">Tagihan Pending Terbaru</h3>
                <a href="{{ route('tagihan.index', ['status' => 'belum_lunas']) }}" class="text-sm font-medium text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-content-tertiary text-left bg-base-input">
                            <th class="px-6 py-3 font-semibold">Pelanggan</th>
                            <th class="px-6 py-3 font-semibold">Periode</th>
                            <th class="px-6 py-3 font-semibold text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse($pending as $item)
                        <tr class="hover:bg-base-page transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-content-primary block">{{ $item->pelanggan->nama_pelanggan }}</span>
                                <span class="text-[10px] text-content-tertiary">{{ $item->pelanggan->desa }}</span>
                            </td>
                            <td class="px-6 py-4 text-content-secondary">
                                {{ date('M', mktime(0, 0, 0, $item->bulan, 1)) }} {{ $item->tahun }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-content-primary">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-content-tertiary">Tidak ada tagihan pending.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Side Area: Stats & Info --}}
    <div class="space-y-8">
        {{-- Progress Card --}}
        <div class="card-dark p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            
            <div class="relative z-10">
                <h4 class="text-lg font-semibold mb-6">Efisiensi Penagihan</h4>
                
                @php 
                    $percent = $stats['tagihan_bulan'] > 0 ? round(($stats['lunas'] / $stats['tagihan_bulan']) * 100) : 0;
                @endphp
                
                <div class="flex items-center justify-center mb-8">
                    <div class="relative flex items-center justify-center">
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/10" />
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="351.85" stroke-dashoffset="{{ 351.85 - (351.85 * $percent / 100) }}" class="text-white transition-all duration-1000" />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span class="text-3xl font-bold">{{ $percent }}%</span>
                            <span class="text-[10px] text-white/70 uppercase tracking-widest font-bold">Lunas</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/10 border border-white/20">
                        <span class="text-xs text-white/80">Total Target</span>
                        <span class="text-sm font-bold">{{ $stats['tagihan_bulan'] }} Pelanggan</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/10 border border-white/20">
                        <span class="text-xs text-white/80">Terselesaikan</span>
                        <span class="text-sm font-bold">{{ $stats['lunas'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links / Help --}}
        <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10">
            <h4 class="text-sm font-bold text-primary uppercase tracking-widest mb-4">Butuh Bantuan?</h4>
            <div class="space-y-3">
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-white border border-border hover:border-primary transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-sm font-medium text-content-secondary group-hover:text-content-primary">Panduan Pengguna</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-white border border-border hover:border-primary transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-status-success/10 text-status-success flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-content-secondary group-hover:text-content-primary">Hubungi Support</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const labels = @json(collect($tren)->pluck('bulan'));
    const data = @json(collect($tren)->pluck('total'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data,
                backgroundColor: '#3B82F6', // primary color
                borderRadius: 8,
                barThickness: 32,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9', drawBorder: false },
                    ticks: {
                        callback: (val) => 'Rp ' + val.toLocaleString('id-ID'),
                        color: '#94A3B8',
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#94A3B8', font: { size: 10 } }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
