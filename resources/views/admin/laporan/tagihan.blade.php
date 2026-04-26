{{-- resources/views/admin/laporan/tagihan.blade.php --}}
@extends('layouts.app')
@section('title','Laporan Tagihan')
@section('page_title','Laporan Tagihan')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')

{{-- Header --}}
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Rekap Tagihan</h2>
        <p class="text-sm text-gray-400">Ringkasan data tagihan pelanggan</p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('admin.laporan.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
            ← Kembali
        </a>

        <a href="{{ route('admin.laporan.tagihan.pdf', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all">
            Export PDF
        </a>
    </div>
</div>

{{-- Filter --}}
<form method="GET"
    class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-5 flex flex-wrap gap-4 items-end">

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
        <select name="bulan"
            class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500">
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
            class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500">
            @foreach(range(now()->year, now()->year-3) as $y)
            <option value="{{ $y }}" {{ $tahun==$y ? 'selected':'' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit"
        class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
        Tampilkan
    </button>
</form>

{{-- Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
    @foreach([
        ['Total Tagihan',$summary['total'],'gray'],
        ['Lunas',$summary['lunas'],'emerald'],
        ['Belum Bayar',$summary['belum_bayar'],'amber'],
        ['Total Nominal','Rp '.number_format($summary['nominal']/1000,0,',','.').'K','blue'],
        ['Terkumpul','Rp '.number_format($summary['terkumpul']/1000,0,',','.').'K','green']
    ] as [$lbl,$val,$color])

    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
        <p class="text-xs text-gray-400 mb-1">{{ $lbl }}</p>
        <p class="text-lg font-extrabold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $val }}</p>
    </div>

    @endforeach
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            {{-- Head --}}
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase text-gray-500">
                    <th class="px-5 py-3 text-left">No</th>
                    <th class="px-3 py-3 text-left">No Tagihan</th>
                    <th class="px-3 py-3 text-left">Pelanggan</th>
                    <th class="px-3 py-3 text-center">Pemakaian</th>
                    <th class="px-3 py-3 text-right">Total</th>
                    <th class="px-3 py-3 text-center">Jatuh Tempo</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>

            {{-- Body --}}
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($tagihan as $i => $t)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>

                    <td class="px-3 py-3">
                        <span class="font-mono text-xs text-brand-600 dark:text-brand-400">
                            {{ $t->nomor_tagihan }}
                        </span>
                    </td>

                    <td class="px-3 py-3">
                        <p class="font-medium text-gray-800 dark:text-gray-200">
                            {{ $t->pelanggan->nama_pelanggan }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $t->pelanggan->nomor_pelanggan }}
                        </p>
                    </td>

                    <td class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                        {{ number_format($t->pemakaian,1) }} m³
                    </td>

                    <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white">
                        {{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}
                    </td>

                    <td class="px-3 py-3 text-center text-xs text-gray-500">
                        {{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}
                    </td>

                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $t->statusBadge() }}">
                            {{ $t->statusLabel() }}
                        </span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <p class="text-sm">Tidak ada tagihan pada periode ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

@endsection