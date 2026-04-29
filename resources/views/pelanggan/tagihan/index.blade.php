{{-- resources/views/pelanggan/tagihan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tagihan Saya')
@section('page_title', 'Tagihan Saya')
@section('page_subtitle', 'Riwayat dan status tagihan air Anda')

@section('content')

{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Tagihan</p>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $tagihan->total() }}</p>
        <p class="text-xs text-gray-400 mt-0.5">Semua periode</p>
    </div>
    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-5 border border-amber-100 dark:border-amber-800 shadow-sm">
        <p class="text-xs text-amber-600 dark:text-amber-400 mb-1">Perlu Dibayar</p>
        <p class="text-2xl font-extrabold text-amber-700 dark:text-amber-300">
            {{ $totalBelumBayar > 0 ? 'Rp '.number_format($totalBelumBayar/1000, 0, ',', '.').'K' : 'Lunas' }}
        </p>
        <p class="text-xs text-amber-500 dark:text-amber-500 mt-0.5">Tagihan belum lunas</p>
    </div>
    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl p-5 border border-emerald-100 dark:border-emerald-800 shadow-sm">
        <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-1">Total Sudah Dibayar</p>
        <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">
            Rp {{ number_format($totalLunas/1000000, 1) }}Jt
        </p>
        <p class="text-xs text-emerald-500 mt-0.5">Akumulasi semua periode</p>
    </div>
</div>

{{-- Tagihan Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($tagihan as $t)
    <a href="{{ route('pelanggan.tagihan.show', $t) }}"
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-brand-200 dark:hover:border-brand-700 transition-all duration-200 overflow-hidden group">
        <div class="p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Periode</p>
                    <p class="font-bold text-gray-800 dark:text-white text-base">
                        {{ \App\Services\TagihanService::namaBulan($t->bulan) }} {{ $t->tahun }}
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">
                    {{ $t->statusLabel() }}
                </span>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Pemakaian</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ number_format($t->pemakaian, 1) }} m³</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Jatuh Tempo</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300 {{ $t->tanggal_jatuh_tempo->isPast() && !$t->isLunas() ? 'text-red-500 dark:text-red-400' : '' }}">
                        {{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-50 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Total Tagihan</p>
                    <p class="text-lg font-extrabold {{ $t->isLunas() ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white' }}">
                        {{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}
                    </p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 group-hover:bg-brand-50 dark:group-hover:bg-brand-900/30 flex items-center justify-center transition-all">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>
        @if(!$t->isLunas())
        <div class="px-5 py-2.5 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-100 dark:border-amber-800">
            <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">⚡ Segera lakukan pembayaran</p>
        </div>
        @endif
    </a>
    @empty
    <div class="col-span-3 py-20 text-center">
        <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-400 font-medium">Belum ada tagihan</p>
    </div>
    @endforelse
</div>

@if($tagihan->hasPages())
<div class="mt-4 flex justify-center">
    {{ $tagihan->links() }}
</div>
@endif
@endsection
