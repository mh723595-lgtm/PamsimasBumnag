@extends('layouts.app')

@section('title', 'Riwayat Input Meteran')
@section('page_title', 'Riwayat Input Meteran')
@section('page_subtitle', 'Histori semua input meteran yang Anda lakukan')

@section('content')

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    {{-- Filter --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                <select name="bulan" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                        {{ \App\Services\TagihanService::namaBulan($b) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                <select name="tahun" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua Tahun</option>
                    @foreach(range(now()->year, now()->year - 2) as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Filter</button>
            @if(request()->hasAny(['bulan','tahun']))
            <a href="{{ route('petugas.riwayat.index') }}" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Awal</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akhir</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemakaian</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Input</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($riwayat as $r)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $r->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $r->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-600 dark:text-gray-400">
                        {{ \App\Services\TagihanService::namaBulan($r->bulan) }} {{ $r->tahun }}
                    </td>
                    <td class="px-3 py-3.5 text-center text-gray-600 dark:text-gray-400">{{ number_format($r->angka_awal) }}</td>
                    <td class="px-3 py-3.5 text-center text-gray-600 dark:text-gray-400">{{ number_format($r->angka_akhir) }}</td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="font-bold text-brand-600 dark:text-brand-400">{{ number_format($r->pemakaian, 1) }} m³</span>
                    </td>
                    <td class="px-3 py-3.5 text-center font-semibold text-gray-800 dark:text-white text-xs">
                        @if($r->tagihan)
                        {{ \App\Services\TagihanService::formatRupiah($r->tagihan->total_tagihan) }}
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        @if($r->tagihan)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $r->tagihan->statusBadge() }}">
                            {{ $r->tagihan->statusLabel() }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">Belum ada tagihan</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right text-xs text-gray-400">
                        {{ $r->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-400">Belum ada riwayat input meteran</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($riwayat->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">{{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }}</p>
        <div class="flex gap-1">
            @if(!$riwayat->onFirstPage())
            <a href="{{ $riwayat->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>
            @endif
            @if($riwayat->hasMorePages())
            <a href="{{ $riwayat->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
