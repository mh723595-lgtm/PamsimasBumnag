@extends('layouts.app')
@section('title','Detail Tagihan')
@section('page_title','Detail Tagihan')
@section('page_subtitle', $tagihan->nomor_tagihan)

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.tagihan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>
    @if($tagihan->status !== 'lunas')
    <a href="{{ route('admin.tagihan.edit', $tagihan) }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Update Status
    </a>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Detail Tagihan --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Header Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-brand-800 to-brand-600 p-5">
                <div class="flex items-start justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-brand-200 text-xs mb-0.5">Nomor Tagihan</p>
                        <p class="text-white font-bold text-xl font-mono">{{ $tagihan->nomor_tagihan }}</p>
                        <p class="text-brand-300 text-sm mt-1">Periode: {{ $tagihan->periodeTeks() }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-sm font-bold
                        {{ $tagihan->status==='lunas' ? 'bg-emerald-400/30 text-emerald-100'
                         : ($tagihan->status==='terlambat' ? 'bg-red-400/30 text-red-100'
                         : 'bg-amber-400/30 text-amber-100') }}">
                        {{ $tagihan->statusLabel() }}
                    </span>
                </div>
            </div>

            {{-- Info Pelanggan --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($tagihan->pelanggan->nama_pelanggan, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $tagihan->pelanggan->nomor_pelanggan }} · {{ $tagihan->pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>

            {{-- Meteran & Pemakaian --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Data Pemakaian</h3>
                <div class="grid grid-cols-4 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Angka Awal</p>
                        <p class="font-bold text-gray-800 dark:text-white">{{ $tagihan->meteran ? number_format($tagihan->meteran->angka_awal) : '-' }}</p>
                        <p class="text-xs text-gray-400">m³</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Angka Akhir</p>
                        <p class="font-bold text-gray-800 dark:text-white">{{ $tagihan->meteran ? number_format($tagihan->meteran->angka_akhir) : '-' }}</p>
                        <p class="text-xs text-gray-400">m³</p>
                    </div>
                    <div class="bg-brand-50 dark:bg-brand-900/30 rounded-xl p-3 text-center">
                        <p class="text-xs text-brand-500 mb-1">Pemakaian</p>
                        <p class="font-bold text-brand-700 dark:text-brand-300">{{ number_format($tagihan->pemakaian, 1) }}</p>
                        <p class="text-xs text-brand-400">m³</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 mb-1">Jatuh Tempo</p>
                        <p class="font-bold text-gray-800 dark:text-white text-sm">{{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Rincian Tarif --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Rincian Tagihan</h3>
                <div class="space-y-2">
                    @foreach($rincian as $r)
                    <div class="flex justify-between items-center text-sm py-2 px-3 rounded-lg
                        {{ str_contains($r['blok'],'Admin') ? 'bg-purple-50 dark:bg-purple-900/20' : 'bg-gray-50 dark:bg-gray-800' }}">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $r['blok'] }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $r['note'] }}</span>
                        </div>
                        <span class="font-bold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($r['biaya']) }}</span>
                    </div>
                    @endforeach

                    @if($tagihan->hasDenda())
                    <div class="flex justify-between items-center text-sm py-2 px-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                        <span class="font-medium text-red-700 dark:text-red-300">⚠️ Denda Keterlambatan</span>
                        <span class="font-bold text-red-700 dark:text-red-300">{{ \App\Services\TagihanService::formatRupiah($tagihan->denda) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center text-sm py-3 px-3 rounded-lg bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800">
                        <span class="font-bold text-brand-800 dark:text-brand-200">TOTAL BAYAR</span>
                        <span class="font-extrabold text-brand-700 dark:text-brand-300 text-lg">
                            {{ \App\Services\TagihanService::formatRupiah($tagihan->total_bayar ?: $tagihan->total_tagihan) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- FIX: Info Pembayaran — tampil jika sudah ada record Pembayaran --}}
            @if($tagihan->pembayaran)
            <div class="p-5 bg-emerald-50 dark:bg-emerald-900/10">
                <h3 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-3">✅ Informasi Pembayaran</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. Pembayaran</p>
                        <p class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->nomor_pembayaran }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Tanggal Bayar</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->tanggal_bayar->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Metode</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 capitalize">{{ $tagihan->pembayaran->metode_bayar }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Dikonfirmasi oleh</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $tagihan->pembayaran->dikonfirmasiOleh?->name ?? '-' }}</p>
                    </div>
                    @if($tagihan->pembayaran->catatan)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 mb-0.5">Catatan</p>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $tagihan->pembayaran->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar Kanan --}}
    <div class="space-y-4">

        {{-- Quick Action --}}
        @if($tagihan->status !== 'lunas')
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">⚡ Konfirmasi Cepat</h3>
            <form method="POST" action="{{ route('admin.tagihan.update', $tagihan) }}"
                onsubmit="return confirm('Konfirmasi tagihan ini sebagai LUNAS? Record pembayaran otomatis akan dibuat.')">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="lunas">
                <input type="hidden" name="metode_bayar" value="tunai">
                <input type="hidden" name="tanggal_bayar" value="{{ now()->toDateString() }}">
                <input type="hidden" name="catatan" value="Dikonfirmasi lunas oleh admin">
                <button type="submit"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Konfirmasi Lunas
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2 text-center">Atau pilih <a href="{{ route('admin.tagihan.edit', $tagihan) }}" class="text-brand-600 hover:underline">Update Status</a> untuk opsi lebih lengkap</p>
        </div>
        @endif

        {{-- Info Tagihan --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">📋 Info Tagihan</h3>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Tanggal Terbit</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $tagihan->tanggal_tagihan->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Jatuh Tempo</span>
                    <span class="font-medium {{ $tagihan->tanggal_jatuh_tempo->isPast() && !$tagihan->isLunas() ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}
                        @if($tagihan->tanggal_jatuh_tempo->isPast() && !$tagihan->isLunas())
                        <span class="text-xs">({{ $tagihan->tanggal_jatuh_tempo->diffForHumans() }})</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Tagihan Pokok</span>
                    <span class="font-semibold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($tagihan->total_tagihan) }}</span>
                </div>
                @if($tagihan->hasDenda())
                <div class="flex justify-between">
                    <span class="text-red-500">+ Denda</span>
                    <span class="font-semibold text-red-600">{{ \App\Services\TagihanService::formatRupiah($tagihan->denda) }}</span>
                </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Total Bayar</span>
                    <span class="font-extrabold text-brand-700 dark:text-brand-300">{{ \App\Services\TagihanService::formatRupiah($tagihan->total_bayar ?: $tagihan->total_tagihan) }}</span>
                </div>
            </div>
        </div>

        {{-- Link ke pembayaran jika ada --}}
        @if($tagihan->pembayaran)
        <a href="{{ route('admin.pembayaran.show', $tagihan->pembayaran) }}"
            class="flex items-center gap-3 p-4 bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800 rounded-2xl hover:bg-brand-100 dark:hover:bg-brand-900/30 transition-all">
            <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-brand-800 dark:text-brand-200">Lihat Detail Pembayaran</p>
                <p class="text-xs text-brand-600 dark:text-brand-400 font-mono">{{ $tagihan->pembayaran->nomor_pembayaran }}</p>
            </div>
            <svg class="w-4 h-4 text-brand-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>
</div>
@endsection