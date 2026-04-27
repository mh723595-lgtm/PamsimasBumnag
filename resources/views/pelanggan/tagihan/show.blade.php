{{-- resources/views/pelanggan/tagihan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('page_title', 'Detail Tagihan')
@section('page_subtitle', $tagihan->nomor_tagihan)

@section('content')
<div class="mb-4">
    <a href="{{ route('pelanggan.tagihan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Tagihan
    </a>
</div>

<div class="max-w-2xl space-y-4">
    {{-- Header --}}
    <div class="bg-gradient-to-br from-brand-800 to-brand-600 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-brand-200 text-xs uppercase tracking-wider mb-1">Nomor Tagihan</p>
                <p class="font-mono font-bold text-lg">{{ $tagihan->nomor_tagihan }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $tagihan->statusBadge() }}">
                {{ $tagihan->statusLabel() }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-brand-300 text-xs">Periode</p>
                <p class="font-semibold">{{ \App\Services\TagihanService::namaBulan($tagihan->bulan) }} {{ $tagihan->tahun }}</p>
            </div>
            <div>
                <p class="text-brand-300 text-xs">Jatuh Tempo</p>
                <p class="font-semibold {{ $tagihan->tanggal_jatuh_tempo->isPast() && !$tagihan->isLunas() ? 'text-red-300' : '' }}">
                    {{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}
                </p>
            </div>
            <div>
                <p class="text-brand-300 text-xs">Pemakaian</p>
                <p class="font-bold text-teal-300 text-lg">{{ number_format($tagihan->pemakaian, 1) }} m³</p>
            </div>
        </div>
    </div>

    {{-- Rincian Tarif --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-4">Rincian Perhitungan</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="text-left px-3 py-2.5 text-xs font-semibold text-gray-500 rounded-l-lg">Komponen</th>
                    <th class="text-center px-3 py-2.5 text-xs font-semibold text-gray-500">Volume</th>
                    <th class="text-center px-3 py-2.5 text-xs font-semibold text-gray-500">Keterangan</th>
                    <th class="text-right px-3 py-2.5 text-xs font-semibold text-gray-500 rounded-r-lg">Biaya</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($rincian as $r)
                <tr>
                    <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300 text-sm">{{ $r['blok'] }}</td>
                    <td class="px-3 py-2.5 text-center text-gray-500 text-sm">
                        {{ is_numeric($r['volume']) ? number_format($r['volume'], 1).' m³' : '-' }}
                    </td>
                    <td class="px-3 py-2.5 text-center text-xs text-gray-400">{{ $r['note'] }}</td>
                    <td class="px-3 py-2.5 text-right font-semibold text-gray-800 dark:text-white">
                        Rp {{ number_format($r['biaya'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-brand-50 dark:bg-brand-900/30">
                    <td colspan="3" class="px-3 py-3 font-bold text-brand-800 dark:text-brand-200 rounded-l-xl">TOTAL</td>
                    <td class="px-3 py-3 text-right font-extrabold text-brand-700 dark:text-brand-300 text-lg rounded-r-xl">
                        {{ \App\Services\TagihanService::formatRupiah($tagihan->total_tagihan) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Meteran Info --}}
    @if($tagihan->meteran)
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">Data Meteran</h3>
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">Angka Awal</p>
                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($tagihan->meteran->angka_awal) }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                <p class="text-xs text-gray-400 mb-1">Angka Akhir</p>
                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($tagihan->meteran->angka_akhir) }}</p>
            </div>
            <div class="bg-brand-50 dark:bg-brand-900/30 rounded-xl p-3 text-center">
                <p class="text-xs text-brand-500 mb-1">Pemakaian</p>
                <p class="text-lg font-bold text-brand-700 dark:text-brand-300">{{ number_format($tagihan->pemakaian, 1) }} m³</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Status Pembayaran --}}
    @if($tagihan->isLunas() && $tagihan->pembayaran)
    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800 p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-emerald-800 dark:text-emerald-200">Pembayaran Dikonfirmasi</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ $tagihan->pembayaran->tanggal_bayar->format('d M Y') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs text-emerald-500">No. Pembayaran</p>
                <p class="font-mono font-semibold text-emerald-800 dark:text-emerald-200">{{ $tagihan->pembayaran->nomor_pembayaran }}</p>
            </div>
            <div>
                <p class="text-xs text-emerald-500">Metode</p>
                <p class="font-semibold text-emerald-800 dark:text-emerald-200 capitalize">{{ $tagihan->pembayaran->metode_bayar }}</p>
            </div>
        </div>
    </div>
    @elseif(!$tagihan->isLunas())
    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800 p-5">
        <p class="font-bold text-amber-800 dark:text-amber-200 mb-1">Tagihan Belum Dibayar</p>
        <p class="text-sm text-amber-600 dark:text-amber-400">Silakan hubungi petugas PAMSIMAS untuk melakukan pembayaran sebelum tanggal {{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}.</p>
    </div>
    @endif
</div>
@endsection