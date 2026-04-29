@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')
@section('page_title', 'Manajemen Pembayaran')
@section('page_subtitle', 'Konfirmasi dan rekap semua pembayaran tagihan')

@section('content')

{{-- STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Menunggu Konfirmasi</p>
        <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Dikonfirmasi</p>
        <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['konfirmasi']) }}</p>
    </div>
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Ditolak</p>
        <p class="text-2xl font-extrabold text-red-600 dark:text-red-400">{{ number_format($stats['ditolak']) }}</p>
    </div>
    <div class="stat-card bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Pendapatan</p>
        <p class="text-2xl font-extrabold text-brand-600 dark:text-brand-400">Rp {{ number_format($stats['total']/1000000, 1) }}Jt</p>
    </div>
</div>

{{-- KONFIRMASI PEMBAYARAN MANUAL --}}
{{-- Form untuk admin input pembayaran langsung dari tagihan yang belum lunas --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm mb-4 overflow-hidden"
    x-data="{ expandForm: false }">
    <button @click="expandForm = !expandForm"
        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="text-left">
                <p class="font-bold text-gray-800 dark:text-white text-sm">Input Pembayaran Manual</p>
                <p class="text-xs text-gray-400">Konfirmasi pembayaran langsung dari tagihan pelanggan</p>
            </div>
        </div>
        <svg :class="expandForm ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="expandForm" x-transition class="border-t border-gray-100 dark:border-gray-800 p-5">
        <form method="GET" action="{{ route('admin.tagihan.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Cari Tagihan Belum Lunas</label>
                <div class="relative">
                    <input type="text" name="search" placeholder="Nama pelanggan / no. tagihan..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <input type="hidden" name="status" value="belum_bayar">
            <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
                Cari Tagihan
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-2">💡 Setelah menemukan tagihan, klik <strong>Detail</strong> untuk konfirmasi pembayaran dari halaman tagihan.</p>
    </div>
</div>

{{-- TABLE PEMBAYARAN --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

    {{-- Filter --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="No. pembayaran / nama pelanggan..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua</option>
                    <option value="pending"    {{ request('status')==='pending'    ? 'selected':'' }}>Pending</option>
                    <option value="konfirmasi" {{ request('status')==='konfirmasi' ? 'selected':'' }}>Dikonfirmasi</option>
                    <option value="ditolak"    {{ request('status')==='ditolak'    ? 'selected':'' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Metode</label>
                <select name="metode" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua</option>
                    <option value="tunai"    {{ request('metode')==='tunai'    ? 'selected':'' }}>Tunai</option>
                    <option value="transfer" {{ request('metode')==='transfer' ? 'selected':'' }}>Transfer</option>
                    <option value="lainnya"  {{ request('metode')==='lainnya'  ? 'selected':'' }}>Lainnya</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Filter</button>
            @if(request()->hasAny(['search','status','metode']))
            <a href="{{ route('admin.pembayaran.index') }}" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Pembayaran</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan</th>
                    <th class="text-right px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Bayar</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pembayaran as $b)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors {{ $b->status==='pending' ? 'bg-amber-50/30 dark:bg-amber-900/5' : '' }}">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400">{{ $b->nomor_pembayaran }}</span>
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $b->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $b->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <a href="{{ route('admin.tagihan.show', $b->tagihan) }}"
                            class="font-mono text-xs text-brand-600 dark:text-brand-400 hover:underline">
                            {{ $b->tagihan->nomor_tagihan }}
                        </a>
                        <p class="text-xs text-gray-400">{{ \App\Services\TagihanService::namaBulan($b->tagihan->bulan) }} {{ $b->tagihan->tahun }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-right font-bold text-gray-800 dark:text-white">
                        {{ \App\Services\TagihanService::formatRupiah($b->jumlah_bayar) }}
                    </td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-500 dark:text-gray-400">
                        {{ $b->tanggal_bayar->format('d/m/Y') }}
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 capitalize">
                            {{ $b->metode_bayar }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $b->status==='konfirmasi' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300'
                             : ($b->status==='pending'    ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
                             : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300') }}">
                            {{ ucfirst($b->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.pembayaran.show', $b) }}"
                                class="p-1.5 rounded-lg text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Tombol Konfirmasi cepat (hanya jika pending) --}}
                            @if($b->status === 'pending')
                            <form method="POST" action="{{ route('admin.pembayaran.update', $b) }}"
                                onsubmit="return confirm('Konfirmasi pembayaran {{ $b->nomor_pembayaran }}?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="konfirmasi">
                                <button type="submit"
                                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all" title="Konfirmasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.pembayaran.update', $b) }}"
                                onsubmit="return confirm('Tolak pembayaran {{ $b->nomor_pembayaran }}?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="ditolak">
                                <button type="submit"
                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Tolak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <svg class="w-14 h-14 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-400 font-medium">Tidak ada data pembayaran</p>
                        <p class="text-gray-300 dark:text-gray-600 text-sm mt-1">Coba ubah filter pencarian</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Menampilkan {{ $pembayaran->firstItem() }}–{{ $pembayaran->lastItem() }} dari {{ $pembayaran->total() }}
        </p>
        <div class="flex gap-1">
            @if(!$pembayaran->onFirstPage())
            <a href="{{ $pembayaran->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>
            @endif
            @if($pembayaran->hasMorePages())
            <a href="{{ $pembayaran->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
