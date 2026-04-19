{{-- resources/views/petugas/meteran/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Meteran')
@section('page_title', 'Detail Input Meteran')

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.meteran.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>

<div class="max-w-2xl space-y-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-brand-800 to-brand-600 p-5">
            <p class="text-brand-200 text-xs mb-1">{{ $meteranAir->pelanggan->nama_pelanggan }}</p>
            <p class="text-white font-bold text-lg">{{ $meteranAir->pelanggan->nomor_pelanggan }}</p>
            <p class="text-brand-300 text-sm mt-1">
                Periode: {{ \App\Services\TagihanService::namaBulan($meteranAir->bulan) }} {{ $meteranAir->tahun }}
            </p>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-4 gap-3 mb-5">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Angka Awal</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($meteranAir->angka_awal) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Angka Akhir</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($meteranAir->angka_akhir) }}</p>
                </div>
                <div class="bg-brand-50 dark:bg-brand-900/30 rounded-xl p-3 text-center">
                    <p class="text-xs text-brand-500 mb-1">Pemakaian</p>
                    <p class="text-xl font-bold text-brand-700 dark:text-brand-300">{{ number_format($meteranAir->pemakaian, 1) }}</p>
                    <p class="text-xs text-brand-400">m³</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Tgl Baca</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $meteranAir->tanggal_baca->format('d/m/Y') }}</p>
                </div>
            </div>

            @if($meteranAir->keterangan)
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl text-sm text-gray-600 dark:text-gray-400">
                <p class="text-xs font-semibold text-gray-400 mb-1">Keterangan</p>
                {{ $meteranAir->keterangan }}
            </div>
            @endif

            @if($meteranAir->tagihan)
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-emerald-600 font-semibold">Tagihan Generated</p>
                        <p class="font-mono text-sm font-bold text-emerald-800 dark:text-emerald-200">{{ $meteranAir->tagihan->nomor_tagihan }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-emerald-600">Total Tagihan</p>
                        <p class="text-xl font-extrabold text-emerald-700 dark:text-emerald-300">
                            {{ \App\Services\TagihanService::formatRupiah($meteranAir->tagihan->total_tagihan) }}
                        </p>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $meteranAir->tagihan->statusBadge() }}">
                        {{ $meteranAir->tagihan->statusLabel() }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection