@extends('layouts.app')
@section('title','Laporan Pembayaran')
@section('page_title','Laporan Pembayaran Air')
@section('page_subtitle', \App\Services\TagihanService::namaBulan($bulan) . ' ' . $tahun)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
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
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            <option value="">Semua Status</option>
            <option value="lunas" {{ request('status')=='lunas' ? 'selected':'' }}>Lunas</option>
            <option value="belum_lunas" {{ request('status')=='belum_lunas' ? 'selected':'' }}>Belum Lunas</option>
        </select>
    </div>
    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Tampilkan</button>
    <a href="{{ route('admin.laporan.pembayaran.export', ['bulan'=>$bulan,'tahun'=>$tahun,'status'=>request('status')]) }}"
       class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow transition-all inline-flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
    </a>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    @foreach([
        ['Total Tagihan',   $summary['total_tagihan'],                                     'blue'],
        ['Sudah Lunas',     $summary['sudah_lunas'],                                       'emerald'],
        ['Belum Lunas',     $summary['belum_lunas'],                                       'rose'],
        ['Total Pendapatan','Rp '.number_format($summary['total_pendapatan'],0,',','.'),   'purple'],
    ] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-0.5">{{ $l }}</p>
        <p class="font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $v }}</p>
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
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemakaian</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Bayar</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kasir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($tagihan as $i => $t)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>

                    <td class="px-3 py-3">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $t->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $t->pelanggan->nomor_pelanggan }}</p>
                    </td>

                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">
                        {{ number_format($t->pemakaian, 1) }} m³
                    </td>

                    <td class="px-3 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">
                        Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                    </td>

                    <td class="px-3 py-3 text-center">
                        @if($t->status_pembayaran === 'lunas')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Lunas
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                Belum Lunas
                            </span>
                        @endif
                    </td>

                    <td class="px-3 py-3 text-center text-xs text-gray-500">
                        {{ $t->tanggal_bayar ? $t->tanggal_bayar->format('d/m/Y') : '-' }}
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $t->kasir?->nama ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        Tidak ada data pembayaran untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>

            {{-- Footer total --}}
            @if($tagihan->count())
            <tfoot>
                <tr class="bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-800">
                    <td colspan="3" class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</td>
                    <td class="px-3 py-3 text-right font-extrabold text-brand-600 dark:text-brand-400">
                        Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection