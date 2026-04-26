@extends('layouts.app')
@section('title','Laporan Pemakaian')
@section('page_title','Laporan Pemakaian Air')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')

{{-- Header --}}
<div class="mb-5 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.laporan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

    {{-- Export PDF --}}
    <a href="{{ route('admin.laporan.pemakaian.pdf', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 
               bg-red-600 hover:bg-red-700 
               text-white text-sm font-bold 
               rounded-xl shadow transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export PDF
    </a>
</div>

{{-- Filter --}}
<form method="GET"
    class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
        <select name="bulan"
            class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(1,12) as $b)
            <option value="{{ $b }}" {{ $bulan==$b ? 'selected':'' }}>
                {{ \App\Services\TagihanService::namaBulan($b) }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
        <select name="tahun"
            class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            @foreach(range(now()->year, now()->year-3) as $y)
            <option value="{{ $y }}" {{ $tahun==$y ? 'selected':'' }}>
                {{ $y }}
            </option>
            @endforeach
        </select>
    </div>

    <button type="submit"
        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
        Tampilkan
    </button>
</form>

{{-- Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Total Pelanggan',$summary['total_pelanggan'],'blue'],
        ['Sudah Input',$summary['sudah_diinput'],'emerald'],
        ['Total Volume',number_format($summary['total_volume'],1).' m³','teal'],
        ['Rata-rata',number_format($summary['rata_rata'],1).' m³','purple']
    ] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">{{ $l }}</p>
        <p class="font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400 text-lg">{{ $v }}</p>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">No</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Angka Awal</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Angka Akhir</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Pemakaian</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Tgl Baca</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Petugas</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($meteran as $i => $m)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>

                    <td class="px-3 py-3">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">
                            {{ $m->pelanggan->nama_pelanggan }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $m->pelanggan->nomor_pelanggan }}
                        </p>
                    </td>

                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">
                        {{ number_format($m->angka_awal) }}
                    </td>

                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">
                        {{ number_format($m->angka_akhir) }}
                    </td>

                    <td class="px-3 py-3 text-center">
                        <span class="font-bold text-brand-600 dark:text-brand-400">
                            {{ number_format($m->pemakaian,1) }} m³
                        </span>
                    </td>

                    <td class="px-3 py-3 text-center text-xs text-gray-500">
                        {{ $m->tanggal_baca->format('d/m/Y') }}
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $m->petugas?->nama_petugas ?? '-' }}
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-3"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>

                        <p class="text-gray-400">
                            Tidak ada data pemakaian untuk periode ini
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection