@extends('layouts.app')
@section('title','Detail Meteran')
@section('page_title','Detail Input Meteran')

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
        {{-- Header --}}
        <div class="bg-gradient-to-r from-brand-800 to-brand-600 p-5">
            <p class="text-brand-200 text-xs mb-0.5">{{ $meteranAir->pelanggan->nama_pelanggan }}</p>
            <p class="text-white font-bold text-lg font-mono">{{ $meteranAir->pelanggan->nomor_pelanggan }}</p>
            <p class="text-brand-300 text-sm mt-1">
                Periode: {{ \App\Services\TagihanService::namaBulan($meteranAir->bulan) }} {{ $meteranAir->tahun }}
            </p>
        </div>

        {{-- Angka Meteran --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <div class="grid grid-cols-4 gap-3">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Angka Awal</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($meteranAir->angka_awal) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Angka Akhir</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($meteranAir->angka_akhir) }}</p>
                </div>
                <div class="bg-brand-50 dark:bg-brand-900/30 rounded-xl p-3 text-center">
                    <p class="text-xs text-brand-500 dark:text-brand-400 mb-1">Pemakaian</p>
                    <p class="text-xl font-bold text-brand-700 dark:text-brand-300">{{ number_format($meteranAir->pemakaian, 1) }}</p>
                    <p class="text-xs text-brand-400">m³</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Tgl Baca</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $meteranAir->tanggal_baca->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Foto Meter --}}
        @if($meteranAir->hasFoto())
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Foto Bukti Meteran
            </h3>
            <img src="{{ $meteranAir->fotoUrl() }}"
                alt="Foto meteran {{ $meteranAir->pelanggan->nomor_pelanggan }}"
                class="w-full max-h-64 object-cover rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm cursor-pointer"
                onclick="this.classList.toggle('max-h-64'); this.classList.toggle('max-h-full')">
            <p class="text-xs text-gray-400 mt-2">Klik foto untuk memperbesar</p>
        </div>
        @else
        <div class="p-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2 text-gray-400 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Tidak ada foto meteran
            </div>
        </div>
        @endif

        {{-- Keterangan & Petugas --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Petugas</p>
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $meteranAir->petugas?->nama_petugas ?? 'Tidak diketahui' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Waktu Input</p>
                    <p class="text-gray-700 dark:text-gray-300 text-xs">{{ $meteranAir->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($meteranAir->keterangan)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-0.5">Keterangan</p>
                    <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $meteranAir->keterangan }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Tagihan yang digenerate --}}
        @if($meteranAir->tagihan)
        <div class="p-5 bg-emerald-50 dark:bg-emerald-900/10">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mb-0.5">Tagihan Generated</p>
                    <p class="font-mono text-sm font-bold text-emerald-800 dark:text-emerald-200">{{ $meteranAir->tagihan->nomor_tagihan }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Total Tagihan</p>
                    <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">
                        {{ \App\Services\TagihanService::formatRupiah($meteranAir->tagihan->total_tagihan) }}
                    </p>
                    @if($meteranAir->tagihan->hasDenda())
                    <p class="text-xs text-red-600 dark:text-red-400">+ Denda: {{ \App\Services\DendaService::formatDenda($meteranAir->tagihan->denda) }}</p>
                    @endif
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $meteranAir->tagihan->statusBadge() }}">
                    {{ $meteranAir->tagihan->statusLabel() }}
                </span>
                @if($rincian)
                <details class="text-xs text-emerald-600 dark:text-emerald-400 cursor-pointer">
                    <summary class="font-semibold hover:underline">Lihat rincian</summary>
                    <div class="mt-2 space-y-1">
                        @foreach($rincian as $r)
                        <div class="flex justify-between text-emerald-700 dark:text-emerald-300">
                            <span>{{ $r['blok'] }}</span>
                            <span>Rp {{ number_format($r['biaya'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
