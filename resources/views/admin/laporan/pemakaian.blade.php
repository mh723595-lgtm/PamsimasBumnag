@extends('layouts.app')
@section('title','Laporan Pemakaian')
@section('page_title','Laporan Pemakaian Air')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

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

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    @foreach([['Total Pelanggan',$summary['total_pelanggan'],'blue'],['Sudah Input',$summary['sudah_diinput'],'emerald'],['Total Volume',number_format($summary['total_volume'],1).' m³','teal'],['Rata-rata',number_format($summary['rata_rata'],1).' m³','purple']] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-0.5">{{ $l }}</p>
        <p class="font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $v }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 dark:bg-gray-800/60">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Angka Awal</th>
                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Angka Akhir</th>
                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemakaian</th>
                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Baca</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($meteran as $i => $m)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="px-3 py-3">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $m->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $m->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_awal) }}</td>
                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_akhir) }}</td>
                    <td class="px-3 py-3 text-center"><span class="font-bold text-brand-600 dark:text-brand-400">{{ number_format($m->pemakaian,1) }} m³</span></td>
                    <td class="px-3 py-3 text-center text-xs text-gray-500">{{ $m->tanggal_baca->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $m->petugas?->nama_petugas ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Tidak ada data pemakaian untuk periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
