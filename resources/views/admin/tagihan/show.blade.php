@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('page_title', 'Detail Tagihan')
@section('page_subtitle', $tagihan->nomor_tagihan)

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.tagihan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Tagihan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- LEFT: Info Tagihan --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Header Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-brand-800 to-brand-600 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-brand-200 text-xs font-medium uppercase tracking-wider mb-1">Nomor Tagihan</p>
                        <p class="text-white font-mono font-bold text-xl">{{ $tagihan->nomor_tagihan }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $tagihan->statusBadge() }}">
                        {{ $tagihan->statusLabel() }}
                    </span>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-brand-300 text-xs">Periode</p>
                        <p class="text-white font-semibold text-sm">{{ \App\Services\TagihanService::namaBulan($tagihan->bulan) }} {{ $tagihan->tahun }}</p>
                    </div>
                    <div>
                        <p class="text-brand-300 text-xs">Tanggal Tagihan</p>
                        <p class="text-white font-semibold text-sm">{{ $tagihan->tanggal_tagihan->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-brand-300 text-xs">Jatuh Tempo</p>
                        <p class="text-white font-semibold text-sm {{ $tagihan->tanggal_jatuh_tempo->isPast() && !$tagihan->isLunas() ? 'text-red-300' : '' }}">
                            {{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Info Pelanggan --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Informasi Pelanggan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nama</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pelanggan->nama_pelanggan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. Pelanggan</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pelanggan->nomor_pelanggan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Alamat</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $tagihan->pelanggan->alamat }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. HP</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $tagihan->pelanggan->no_hp ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Meteran Info --}}
            @if($tagihan->meteran)
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Data Meteran</h3>
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Angka Awal</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($tagihan->meteran->angka_awal) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Angka Akhir</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($tagihan->meteran->angka_akhir) }}</p>
                    </div>
                    <div class="bg-brand-50 dark:bg-brand-900/30 rounded-xl p-3 text-center">
                        <p class="text-xs text-brand-600 dark:text-brand-400 mb-1">Pemakaian</p>
                        <p class="text-lg font-bold text-brand-700 dark:text-brand-300">{{ number_format($tagihan->pemakaian, 1) }} m³</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Tgl Baca</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $tagihan->meteran->tanggal_baca->format('d/m/Y') }}</p>
                    </div>
                </div>
                @if($tagihan->meteran->petugas)
                <p class="text-xs text-gray-400 mt-3">Dicatat oleh: <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $tagihan->meteran->petugas->nama_petugas }}</span></p>
                @endif
            </div>
            @endif

            {{-- Rincian Tarif --}}
            <div class="p-5">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Rincian Perhitungan Tarif</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <th class="text-left px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase rounded-l-lg">Komponen</th>
                            <th class="text-center px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase">Volume</th>
                            <th class="text-center px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase">Tarif</th>
                            <th class="text-right px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase rounded-r-lg">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($rincian as $r)
                        <tr>
                            <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300">{{ $r['blok'] }}</td>
                            <td class="px-3 py-2.5 text-center text-gray-600 dark:text-gray-400">
                                {{ is_numeric($r['volume']) ? number_format($r['volume'], 1).' m³' : $r['volume'] }}
                            </td>
                            <td class="px-3 py-2.5 text-center text-gray-600 dark:text-gray-400 text-xs">{{ $r['note'] }}</td>
                            <td class="px-3 py-2.5 text-right font-semibold text-gray-800 dark:text-white">
                                Rp {{ number_format($r['biaya'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-brand-50 dark:bg-brand-900/30 rounded-lg">
                            <td colspan="3" class="px-3 py-3 font-bold text-brand-800 dark:text-brand-200 rounded-l-xl">TOTAL TAGIHAN</td>
                            <td class="px-3 py-3 text-right font-extrabold text-brand-700 dark:text-brand-300 text-base rounded-r-xl">
                                {{ \App\Services\TagihanService::formatRupiah($tagihan->total_tagihan) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Pembayaran Info --}}
        @if($tagihan->pembayaran)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Info Pembayaran
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-400">No. Pembayaran</p>
                    <p class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->nomor_pembayaran }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Tanggal Bayar</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->tanggal_bayar->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Metode</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ $tagihan->pembayaran->metode_bayar }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Dikonfirmasi</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->dikonfirmasiOleh?->name ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Aksi --}}
    <div class="space-y-4">
        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Aksi</h3>
            <div class="space-y-2.5">
                <a href="{{ route('admin.tagihan.edit', $tagihan) }}"
                    class="flex items-center gap-3 w-full px-4 py-3 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-xl text-sm font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Status Tagihan
                </a>
                <a href="{{ route('admin.laporan.tagihan') }}?nomor={{ $tagihan->nomor_tagihan }}"
                    class="flex items-center gap-3 w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-700 dark:text-red-300 rounded-xl text-sm font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Cetak / Export PDF
                </a>
            </div>
        </div>

        {{-- Summary Box --}}
        <div class="bg-gradient-to-br from-brand-800 to-brand-600 rounded-2xl p-5 text-white">
            <p class="text-brand-200 text-xs font-medium mb-1">Total Tagihan</p>
            <p class="text-3xl font-extrabold mb-4">{{ \App\Services\TagihanService::formatRupiah($tagihan->total_tagihan) }}</p>
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-brand-300">Pemakaian</span>
                    <span class="font-semibold">{{ number_format($tagihan->pemakaian, 1) }} m³</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-brand-300">Biaya Air</span>
                    <span class="font-semibold">{{ \App\Services\TagihanService::formatRupiah($hasil['biaya_pokok']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-brand-300">Biaya Admin</span>
                    <span class="font-semibold">Rp 2.500</span>
                </div>
                <div class="border-t border-brand-500 mt-2 pt-2 flex justify-between">
                    <span class="text-white font-semibold">Total</span>
                    <span class="font-extrabold">{{ \App\Services\TagihanService::formatRupiah($tagihan->total_tagihan) }}</span>
                </div>
            </div>
        </div>

        {{-- Status Timeline --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Timeline</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tagihan Dibuat</p>
                        <p class="text-xs text-gray-400">{{ $tagihan->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($tagihan->pembayaran)
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Pembayaran Dikonfirmasi</p>
                        <p class="text-xs text-gray-400">{{ $tagihan->pembayaran->tanggal_bayar->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection