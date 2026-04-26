@extends('layouts.app')

@section('title', 'Dashboard Pelanggan')
@section('page_title', 'Dashboard Saya')
@section('page_subtitle', 'Selamat datang, ' . $pelanggan->nama_pelanggan . ' · No. ' . $pelanggan->nomor_pelanggan)

@section('content')

{{-- ALERT TAGIHAN BELUM BAYAR --}}
@if($tagihanBelumBayar->count())
<div class="mb-5 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl flex items-start gap-3">
    <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-amber-800 dark:text-amber-200">Anda memiliki {{ $tagihanBelumBayar->count() }} tagihan yang belum dibayar</p>
        <p class="text-amber-700 dark:text-amber-300 text-sm mt-0.5">
            Total: <strong>Rp {{ number_format($tagihanBelumBayar->sum('total_tagihan'), 0, ',', '.') }}</strong>
        </p>
    </div>
    <a href="{{ route('pelanggan.tagihan.index') }}"
        class="flex-shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl transition-all">
        Bayar Sekarang
    </a>
</div>
@endif

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Tagihan Bulan Ini --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">
            {{ $tagihanBulanIni ? 'Rp '.number_format($tagihanBulanIni->total_tagihan/1000, 0, ',', '.').'.' : '-' }}
        </p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Tagihan Bulan Ini</p>
    </div>

    {{-- Status Tagihan --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4
            {{ $tagihanBulanIni && $tagihanBulanIni->isLunas() ? 'bg-emerald-50 dark:bg-emerald-900/30' : 'bg-amber-50 dark:bg-amber-900/30' }}">
            @if($tagihanBulanIni && $tagihanBulanIni->isLunas())
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @else
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">
            {{ $tagihanBulanIni ? $tagihanBulanIni->statusLabel() : 'Belum Ada' }}
        </p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Status Bulan Ini</p>
    </div>

    {{-- Pemakaian Terakhir --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">
            {{ $meteranTerakhir ? number_format($meteranTerakhir->pemakaian, 1).' m³' : '-' }}
        </p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Pemakaian Terakhir</p>
    </div>

    {{-- Belum Bayar --}}
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $tagihanBelumBayar->count() }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Tagihan Belum Bayar</p>
    </div>
</div>

{{-- CHART + INFO --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">Pemakaian Air Saya</h3>
                <p class="text-xs text-gray-400">6 bulan terakhir (m³)</p>
            </div>
        </div>
        <canvas id="pemakaianChart" height="120"></canvas>
    </div>

    {{-- Info Pelanggan --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">Informasi Akun</h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nomor Pelanggan</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $pelanggan->nomor_pelanggan }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Alamat</p>
                    <p class="text-sm text-gray-800 dark:text-white">{{ $pelanggan->alamat }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 capitalize">
                        {{ $pelanggan->status }}
                    </span>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Terdaftar Sejak</p>
                    <p class="text-sm text-gray-800 dark:text-white">{{ $pelanggan->tanggal_daftar->format('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2">
            <a href="{{ route('pelanggan.tagihan.index') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold transition-all">
                <span>Lihat Tagihan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('pelanggan.pengaduan.create') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold transition-all">
                <span>Buat Pengaduan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? 'rgba(156,163,175,1)' : 'rgba(107,114,128,1)';
    const gridColor = isDark ? 'rgba(55,65,81,0.5)' : 'rgba(229,231,235,0.8)';

    new Chart(document.getElementById('pemakaianChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($chartLabel),
            datasets: [{
                label: 'Pemakaian (m³)',
                data: @json($chartPemakaian),
                borderColor: '#3b93f2',
                backgroundColor: 'rgba(59,147,242,0.1)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#3b93f2',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' m³' } }
            },
            scales: {
                x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 11 }, callback: v => v + ' m³' }, grid: { color: gridColor }, min: 0 }
            }
        }
    });
});
</script>
@endpush