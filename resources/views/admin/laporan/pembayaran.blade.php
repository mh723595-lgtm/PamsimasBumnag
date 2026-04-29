@extends('layouts.app')
@section('title','Laporan Pembayaran')
@section('page_title','Laporan Pembayaran')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.laporan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Laporan
    </a>
    <a href="{{ route('admin.laporan.pembayaran.pdf', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export PDF
    </a>
</div>

{{-- Filter --}}
<form method="GET" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
        <select name="bulan" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(1,12) as $b)
            <option value="{{ $b }}" {{ $bulan==$b ? 'selected':'' }}>{{ \App\Services\TagihanService::namaBulan($b) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
        <select name="tahun" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(now()->year, now()->year-3) as $y)
            <option value="{{ $y }}" {{ $tahun==$y ? 'selected':'' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
        Tampilkan
    </button>
</form>

{{-- Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['label'=>'Total Transaksi',   'val'=>$summary['total'],                                              'color'=>'brand'],
        ['label'=>'Total Pendapatan',  'val'=>'Rp '.number_format($summary['nominal']/1000000,1).'Jt',        'color'=>'emerald'],
        ['label'=>'Via Tunai',         'val'=>'Rp '.number_format($summary['tunai']/1000000,1).'Jt',          'color'=>'blue'],
        ['label'=>'Via Transfer',      'val'=>'Rp '.number_format($summary['transfer']/1000000,1).'Jt',       'color'=>'purple'],
    ] as $s)
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $s['label'] }}</p>
        <p class="font-extrabold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400 text-xl">{{ $s['val'] }}</p>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pembayaran</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Bayar</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pembayaran as $i => $b)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="px-3 py-3">
                        <span class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $b->nomor_pembayaran }}</span>
                    </td>
                    <td class="px-3 py-3">
                        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $b->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $b->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $b->tagihan->nomor_tagihan }}</span>
                        <p class="text-xs text-gray-400">{{ \App\Services\TagihanService::namaBulan($b->tagihan->bulan) }} {{ $b->tagihan->tahun }}</p>
                    </td>
                    <td class="px-3 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ $b->tanggal_bayar->format('d/m/Y') }}
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                            {{ $b->metode_bayar==='tunai' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                             : ($b->metode_bayar==='transfer' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'
                             : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300') }}">
                            {{ $b->metode_bayar }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800 dark:text-white">
                        {{ \App\Services\TagihanService::formatRupiah($b->jumlah_bayar) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-400">Tidak ada pembayaran untuk periode ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($pembayaran->count() > 0)
            <tfoot>
                <tr class="bg-emerald-50 dark:bg-emerald-900/20">
                    <td colspan="6" class="px-5 py-3 text-right font-bold text-emerald-800 dark:text-emerald-200 text-sm">
                        TOTAL {{ strtoupper(\App\Services\TagihanService::namaBulan($bulan)) }} {{ $tahun }}
                    </td>
                    <td class="px-5 py-3 text-right font-extrabold text-emerald-700 dark:text-emerald-300">
                        {{ \App\Services\TagihanService::formatRupiah($summary['nominal']) }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
