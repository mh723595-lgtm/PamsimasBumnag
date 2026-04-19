{{-- resources/views/admin/laporan/tagihan.blade.php --}}
@extends('layouts.app')
@section('title','Laporan Tagihan')
@section('page_title','Laporan Tagihan')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <a href="{{ route('admin.laporan.tagihan.pdf', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
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
        <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
        <select name="bulan" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(1,12) as $b)
            <option value="{{ $b }}" {{ $bulan==$b ? 'selected':'' }}>{{ \App\Services\TagihanService::namaBulan($b) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
        <select name="tahun" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(now()->year, now()->year-3) as $y)
            <option value="{{ $y }}" {{ $tahun==$y ? 'selected':'' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Tampilkan</button>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
    @foreach([['Total Tagihan',$summary['total'],'gray'],['Lunas',$summary['lunas'],'emerald'],['Belum Bayar',$summary['belum_bayar'],'amber'],['Total Nominal','Rp '.number_format($summary['nominal']/1000,0,',','.').'K','blue'],['Terkumpul','Rp '.number_format($summary['terkumpul']/1000,0,',','.').'K','green']] as [$lbl,$val,$color])
    <div class="bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-0.5">{{ $lbl }}</p>
        <p class="font-extrabold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $val }}</p>
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
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Tagihan</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemakaian</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($tagihan as $i => $t)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-3 py-3"><span class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $t->nomor_tagihan }}</span></td>
                    <td class="px-3 py-3">
                        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $t->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $t->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($t->pemakaian, 1) }} m³</td>
                    <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}</td>
                    <td class="px-3 py-3 text-center text-xs text-gray-500">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">{{ $t->statusLabel() }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Tidak ada tagihan untuk periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection