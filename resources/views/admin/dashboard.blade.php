@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan sistem PAMSIMAS — ' . \Carbon\Carbon::now()->translatedFormat('l, d F Y'))

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Pelanggan Aktif --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-green-600 bg-green-50 dark:bg-green-900/30 dark:text-green-400 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($totalPelanggan) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Pelanggan Aktif</p>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-brand-600 bg-brand-50 dark:bg-brand-900/30 dark:text-brand-400 px-2 py-0.5 rounded-full">Bulan Ini</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">Rp {{ number_format($pendapatanBulanIni/1000, 0, ',', '.') }}K</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Pendapatan</p>
    </div>

    {{-- Tagihan Lunas --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            @if($tagihanBulanIni > 0)
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-0.5 rounded-full">
                {{ round($tagihanLunas / max($tagihanBulanIni, 1) * 100) }}%
            </span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($tagihanLunas) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Tagihan Lunas</p>
    </div>

    {{-- Pengaduan Baru --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            @if($pengaduanBaru > 0)
            <span class="text-xs font-semibold text-orange-600 bg-orange-50 dark:bg-orange-900/30 dark:text-orange-400 px-2 py-0.5 rounded-full">Perlu Ditangani</span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($pengaduanBaru) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Pengaduan Baru</p>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    {{-- Pendapatan & Pemakaian Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">Tren Pendapatan & Pemakaian</h3>
                <p class="text-gray-400 text-xs mt-0.5">6 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-brand-500 inline-block"></span>Pendapatan
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-teal-400 inline-block"></span>Pemakaian
                </span>
            </div>
        </div>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    {{-- Status Tagihan Donut --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="mb-5">
            <h3 class="font-bold text-gray-800 dark:text-white">Status Tagihan</h3>
            <p class="text-gray-400 text-xs mt-0.5">Semua periode</p>
        </div>
        <div class="relative">
            <canvas id="donutChart" height="180"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>Lunas
                </span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($tagihanLunasTotal) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>Belum Bayar
                </span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($tagihanBelumBayarTotal) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>Terlambat
                </span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ number_format($tagihanTerlambatTotal) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM TABLES --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Tagihan Terbaru --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 dark:text-white">Tagihan Terbaru</h3>
            <a href="{{ route('admin.tagihan.index') }}" class="text-brand-600 dark:text-brand-400 text-xs font-medium hover:underline">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($tagihanTerbaru as $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-5 py-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $t->pelanggan->nama_pelanggan }}</p>
                                <p class="text-xs text-gray-400">{{ $t->pelanggan->nomor_pelanggan }}</p>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-400 text-xs">
                            {{ \App\Services\TagihanService::namaBulan($t->bulan) }} {{ $t->tahun }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800 dark:text-white text-xs">
                            {{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">
                                {{ $t->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada tagihan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pengaduan Terbaru --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 dark:text-white">Pengaduan Terbaru</h3>
            <a href="{{ route('admin.pengaduan.index') }}" class="text-brand-600 dark:text-brand-400 text-xs font-medium hover:underline">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800">
            @forelse($pengaduanTerbaru as $p)
            <div class="px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ $p->judul }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $p->pelanggan->nama_pelanggan }} · {{ $p->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="flex-shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->statusBadge() }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada pengaduan</div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? 'rgba(156,163,175,1)' : 'rgba(107,114,128,1)';
    const gridColor = isDark ? 'rgba(55,65,81,0.5)' : 'rgba(229,231,235,0.8)';

    // Trend Chart
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($chartLabel),
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: @json($chartPendapatan),
                    backgroundColor: 'rgba(59,147,242,0.7)',
                    borderColor: '#3b93f2',
                    borderWidth: 0,
                    borderRadius: 8,
                    yAxisID: 'y',
                },
                {
                    label: 'Pemakaian (m³)',
                    data: @json($chartPemakaian),
                    type: 'line',
                    borderColor: '#2dd4bf',
                    backgroundColor: 'rgba(45,212,191,0.1)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#2dd4bf',
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1f2937' : '#fff',
                    titleColor: isDark ? '#f9fafb' : '#111827',
                    bodyColor: isDark ? '#9ca3af' : '#6b7280',
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                }
            },
            scales: {
                x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 11 }, callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'Jt' }, grid: { color: gridColor } },
                y1: { type: 'linear', display: true, position: 'right', ticks: { color: '#2dd4bf', font: { size: 11 }, callback: v => v + ' m³' }, grid: { drawOnChartArea: false } },
            }
        }
    });

    // Donut Chart
    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Bayar', 'Terlambat'],
            datasets: [{
                data: [{{ $tagihanLunasTotal }}, {{ $tagihanBelumBayarTotal }}, {{ $tagihanTerlambatTotal }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1f2937' : '#fff',
                    titleColor: isDark ? '#f9fafb' : '#111827',
                    bodyColor: isDark ? '#9ca3af' : '#6b7280',
                    borderColor: isDark ? '#374151' : '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 10,
                }
            }
        }
    });
});
</script>
@endpush
