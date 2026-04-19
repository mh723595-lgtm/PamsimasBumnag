{{-- resources/views/admin/pembayaran/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pembayaran')
@section('page_title','Detail Pembayaran')
@section('page_subtitle',$pembayaran->nomor_pembayaran)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pembayaran.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="max-w-2xl space-y-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-700 to-emerald-500 p-6 text-white">
            <p class="text-emerald-200 text-xs mb-1">Nomor Pembayaran</p>
            <p class="font-mono font-bold text-xl">{{ $pembayaran->nomor_pembayaran }}</p>
            <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
                <div><p class="text-emerald-300 text-xs">Tanggal</p><p class="font-semibold">{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</p></div>
                <div><p class="text-emerald-300 text-xs">Metode</p><p class="font-semibold capitalize">{{ $pembayaran->metode_bayar }}</p></div>
                <div><p class="text-emerald-300 text-xs">Status</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $pembayaran->status==='konfirmasi' ? 'bg-white text-emerald-700'
                            : ($pembayaran->status==='pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($pembayaran->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-5 divide-y divide-gray-50 dark:divide-gray-800 space-y-4">
            {{-- Info Pelanggan --}}
            <div class="pb-4">
                <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Pelanggan</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($pembayaran->pelanggan->nama_pelanggan, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pembayaran->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $pembayaran->pelanggan->nomor_pelanggan }}</p>
                    </div>
                </div>
            </div>

            {{-- Tagihan --}}
            <div class="pt-4 pb-4">
                <p class="text-xs text-gray-400 uppercase font-semibold mb-2">Tagihan Terkait</p>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-mono text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $pembayaran->tagihan->nomor_tagihan }}</p>
                            <p class="text-xs text-gray-400">{{ \App\Services\TagihanService::namaBulan($pembayaran->tagihan->bulan) }} {{ $pembayaran->tagihan->tahun }}</p>
                        </div>
                        <p class="font-extrabold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($pembayaran->tagihan->total_tagihan) }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Bayar --}}
            <div class="pt-4">
                <div class="flex items-center justify-between">
                    <p class="font-bold text-gray-700 dark:text-gray-300">Jumlah Dibayar</p>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ \App\Services\TagihanService::formatRupiah($pembayaran->jumlah_bayar) }}
                    </p>
                </div>
                @if($pembayaran->dikonfirmasiOleh)
                <p class="text-xs text-gray-400 mt-2">Dikonfirmasi oleh: {{ $pembayaran->dikonfirmasiOleh->name }}</p>
                @endif
                @if($pembayaran->catatan)
                <p class="text-xs text-gray-400 mt-1">Catatan: {{ $pembayaran->catatan }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Update Status --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">Update Status Pembayaran</h3>
        <form method="POST" action="{{ route('admin.pembayaran.update', $pembayaran) }}">
            @csrf @method('PUT')
            <div class="flex gap-2 flex-wrap">
                @foreach(['konfirmasi'=>['Konfirmasi','emerald'],'ditolak'=>['Tolak','red']] as $val=>[$label,$color])
                @if($pembayaran->status !== $val)
                <button type="submit" name="status" value="{{ $val }}"
                    class="px-5 py-2.5 bg-{{ $color }}-500 hover:bg-{{ $color }}-600 text-white text-sm font-semibold rounded-xl shadow transition-all"
                    onclick="return confirm('Yakin ubah status ke {{ $label }}?')">
                    {{ $label }} Pembayaran
                </button>
                @endif
                @endforeach
            </div>
        </form>
    </div>
</div>
@endsection