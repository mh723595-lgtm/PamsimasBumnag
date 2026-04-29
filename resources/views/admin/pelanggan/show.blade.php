{{-- resources/views/admin/pelanggan/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pelanggan')
@section('page_title','Detail Pelanggan')
@section('page_subtitle',$pelanggan->nomor_pelanggan)

@section('content')
<div class="mb-4 flex gap-3">
    <a href="{{ route('admin.pelanggan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-700 flex items-center justify-center text-white font-bold text-xl">
                    {{ strtoupper(substr($pelanggan->nama_pelanggan,0,2)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 dark:text-white text-lg">{{ $pelanggan->nama_pelanggan }}</p>
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $pelanggan->nomor_pelanggan }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold mt-1
                        {{ $pelanggan->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}
                        capitalize">{{ $pelanggan->status }}</span>
                </div>
            </div>
            <div class="space-y-3 text-sm">
                @foreach([['Alamat',$pelanggan->alamat],['RT/RW',$pelanggan->rt_rw??'-'],['Desa',$pelanggan->desa??'-'],['No. HP',$pelanggan->no_hp??'-'],['Meteran Awal',number_format($pelanggan->meteran_awal).' m³'],['Tgl Daftar',$pelanggan->tanggal_daftar->format('d/m/Y')],['Email',$pelanggan->user->email]] as [$k,$v])
                <div class="flex justify-between gap-3 py-2 border-b border-gray-50 dark:border-gray-800 last:border-0">
                    <span class="text-gray-400 text-xs flex-shrink-0">{{ $k }}</span>
                    <span class="text-gray-700 dark:text-gray-300 text-right text-xs font-medium">{{ $v }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.pelanggan.edit', $pelanggan) }}" class="flex-1 py-2 text-center text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition-all">Edit</a>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">Riwayat Tagihan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-gray-50 dark:bg-gray-800/60">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Pemakaian</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($pelanggan->tagihanAir as $t)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ \App\Services\TagihanService::namaBulan($t->bulan) }} {{ $t->tahun }}</p>
                                <p class="text-xs text-gray-400">{{ $t->nomor_tagihan }}</p>
                            </td>
                            <td class="px-3 py-3 text-center font-semibold text-brand-600 dark:text-brand-400">{{ number_format($t->pemakaian,1) }} m³</td>
                            <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">{{ $t->statusLabel() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada tagihan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
