@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('page_title', 'Dashboard Petugas')
@section('page_subtitle', 'Selamat datang, ' . auth()->user()->name . ' · ' . \Carbon\Carbon::now()->translatedFormat('d F Y'))

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($meteranBulanIni) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Input Meteran Saya</p>
    </div>

    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($sudahInputMeteran) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Sudah Diinput</p>
    </div>

    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($belumInputMeteran) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum Diinput</p>
    </div>

    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($pengaduanBaru) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Pengaduan Baru</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Chart Pemakaian Harian --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">Pemakaian Harian Bulan Ini</h3>
                <p class="text-xs text-gray-400 mt-0.5">Total volume per hari (m³)</p>
            </div>
            <a href="{{ route('petugas.meteran.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Input Meteran
            </a>
        </div>
        <canvas id="meteranChart" height="120"></canvas>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="space-y-2.5">
            <a href="{{ route('petugas.meteran.create') }}"
                class="flex items-center gap-3 p-3.5 rounded-xl bg-brand-50 dark:bg-brand-900/30 hover:bg-brand-100 dark:hover:bg-brand-900/50 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-brand-800 dark:text-brand-200">Input Meteran</p>
                    <p class="text-xs text-brand-600 dark:text-brand-400">Catat pemakaian air</p>
                </div>
            </a>
            <a href="{{ route('petugas.riwayat.index') }}"
                class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-gray-500 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Lihat Riwayat</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Histori input meteran</p>
                </div>
            </a>
            <a href="{{ route('petugas.pengaduan.index') }}"
                class="flex items-center gap-3 p-3.5 rounded-xl bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-orange-500 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-orange-800 dark:text-orange-200">Tangani Pengaduan</p>
                    <p class="text-xs text-orange-600 dark:text-orange-400">{{ $pengaduanBaru }} pengaduan baru</p>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Meteran Terbaru --}}
@if($meteranTerbaru->count())
<div class="mt-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-bold text-gray-800 dark:text-white">Input Meteran Terakhir Saya</h3>
        <a href="{{ route('petugas.riwayat.index') }}" class="text-brand-600 dark:text-brand-400 text-xs hover:underline">Lihat semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Awal</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akhir</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemakaian</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($meteranTerbaru as $m)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $m->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $m->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3 text-center text-xs text-gray-600 dark:text-gray-400">
                        {{ \App\Services\TagihanService::namaBulan($m->bulan) }} {{ $m->tahun }}
                    </td>
                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_awal) }}</td>
                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_akhir) }}</td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-semibold text-brand-600 dark:text-brand-400">{{ number_format($m->pemakaian, 1) }} m³</span>
                    </td>
                    <td class="px-5 py-3 text-right text-xs text-gray-400">{{ $m->tanggal_baca->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? 'rgba(156,163,175,1)' : 'rgba(107,114,128,1)';
    const gridColor = isDark ? 'rgba(55,65,81,0.5)' : 'rgba(229,231,235,0.8)';

    new Chart(document.getElementById('meteranChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($chartHari),
            datasets: [{
                label: 'Pemakaian (m³)',
                data: @json($chartPemakaian),
                backgroundColor: 'rgba(59,147,242,0.7)',
                borderColor: '#3b93f2',
                borderRadius: 6,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => ctx.parsed.y + ' m³' }
                }
            },
            scales: {
                x: { ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 10 }, callback: v => v + ' m³' }, grid: { color: gridColor } }
            }
        }
    });
});
</script>
@endpush
