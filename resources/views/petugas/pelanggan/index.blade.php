@extends('layouts.app')
@section('title', 'Pelanggan Saya')
@section('page_title', 'Pelanggan Saya')
@section('page_subtitle', 'Daftar pelanggan yang ditugaskan kepada Anda')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Pelanggan</p>
        <p class="text-3xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">semua status</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Pelanggan Aktif</p>
        <p class="text-3xl font-bold text-emerald-500">{{ $stats['aktif'] }}</p>
        <p class="text-xs text-gray-400 mt-1">status aktif</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Ada Tunggakan</p>
        <p class="text-3xl font-bold text-amber-500">{{ $stats['tunggakan'] }}</p>
        <p class="text-xs text-gray-400 mt-1">tagihan belum bayar</p>
    </div>
</div>

{{-- Filter & Tabel --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex-wrap gap-3">
        <h2 class="font-semibold text-gray-800 dark:text-white text-sm">
            Daftar Pelanggan
            @if($petugas)
                <span class="ml-2 px-2 py-0.5 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 rounded-lg text-xs font-medium">
                    {{ $petugas->nama_petugas }}
                </span>
            @endif
        </h2>
        <form method="GET" action="{{ route('petugas.pelanggan.index') }}"
            class="flex items-center gap-2 flex-wrap">
            <select name="status"
                class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400 text-gray-700 dark:text-gray-300">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama / nomor / HP..."
                class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-1.5 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-brand-400">
            <button type="submit"
                class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Cari
            </button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('petugas.pelanggan.index') }}"
                class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jorong</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Meteran Awal</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pelangganList as $plg)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600
                                flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($plg->nama_pelanggan, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $plg->nama_pelanggan }}</p>
                                <p class="text-xs text-gray-400">{{ $plg->nomor_pelanggan }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5">
                        @if($plg->jorong)
                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg text-xs font-medium">
                            {{ $plg->jorong->nama_jorong }}
                        </span>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="text-gray-700 dark:text-gray-300 text-xs">{{ $plg->no_hp ?? '—' }}</p>
                        <p class="text-gray-400 text-xs truncate max-w-[160px]">{{ $plg->alamat }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-gray-600 dark:text-gray-400 text-sm font-mono">
                        {{ number_format($plg->meteran_awal) }}
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        @if($plg->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                            {{ ucfirst($plg->status) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <a href="{{ route('petugas.meteran.create', ['pelanggan_id' => $plg->id]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold
                            text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Input Meteran
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            @if(request()->hasAny(['search','status']))
                                <p class="text-sm font-medium text-gray-500">Tidak ada hasil untuk pencarian ini</p>
                                <a href="{{ route('petugas.pelanggan.index') }}"
                                    class="text-xs text-brand-600 hover:underline">Reset filter</a>
                            @else
                                <p class="text-sm font-medium text-gray-500">Belum ada pelanggan yang diassign ke Anda</p>
                                <p class="text-xs text-gray-400">Hubungi admin untuk mendapatkan assignment pelanggan</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pelangganList->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $pelangganList->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection