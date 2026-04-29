@extends('layouts.app')

@section('title', 'Riwayat Pemakaian')
@section('page_title', 'Riwayat Pemakaian & Pembayaran')
@section('page_subtitle', 'Histori pemakaian air dan pembayaran tagihan Anda')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Riwayat Meteran --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Riwayat Pemakaian Air</h3>
                <p class="text-xs text-gray-400 mt-0.5">Data meteran tiap bulan</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Awal</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akhir</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pakai</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($riwayatMeteran as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-800 dark:text-gray-200">
                                {{ \App\Services\TagihanService::namaBulan($m->bulan) }} {{ $m->tahun }}
                                <p class="text-xs text-gray-400 font-normal">{{ $m->tanggal_baca->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-3 py-3.5 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_awal) }}</td>
                            <td class="px-3 py-3.5 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_akhir) }}</td>
                            <td class="px-3 py-3.5 text-center">
                                <span class="font-bold text-brand-600 dark:text-brand-400">{{ number_format($m->pemakaian, 1) }} m³</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($m->tagihan)
                                <p class="font-bold text-gray-800 dark:text-white text-xs">{{ \App\Services\TagihanService::formatRupiah($m->tagihan->total_tagihan) }}</p>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $m->tagihan->statusBadge() }}">
                                    {{ $m->tagihan->statusLabel() }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">Belum ada riwayat pemakaian</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($riwayatMeteran->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $riwayatMeteran->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Riwayat Pembayaran</h3>
                <p class="text-xs text-gray-400 mt-0.5">10 pembayaran terakhir</p>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($riwayatBayar as $b)
                <div class="px-5 py-3.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400">{{ $b->nomor_pembayaran }}</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-0.5">
                                {{ \App\Services\TagihanService::formatRupiah($b->jumlah_bayar) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $b->tanggal_bayar->format('d M Y') }} · {{ ucfirst($b->metode_bayar) }}</p>
                        </div>
                        <span class="flex-shrink-0 px-2 py-1 rounded-full text-xs font-semibold
                            {{ $b->status === 'konfirmasi' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                            {{ ucfirst($b->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center text-gray-400 text-sm">
                    Belum ada riwayat pembayaran
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
