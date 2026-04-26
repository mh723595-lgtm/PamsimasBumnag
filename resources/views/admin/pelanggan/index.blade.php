{{-- resources/views/admin/pelanggan/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Pelanggan')
@section('page_title','Manajemen Pelanggan')
@section('page_subtitle','Kelola data semua pelanggan')

@section('content')

{{-- Summary --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([['Total',$stats['total'],'blue'],['Aktif',$stats['aktif'],'emerald'],['Nonaktif',$stats['nonaktif'],'red']] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
        <p class="text-xs text-gray-400 mb-1">{{ $l }}</p>
        <p class="text-xl font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $v }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

    {{-- Header Table --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-center justify-between">

        {{-- Filter --}}
        <form method="GET" class="flex flex-wrap gap-3 items-center">

            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari pelanggan..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500 transition w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="status"
                class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500 transition">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status')==='aktif' ? 'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Nonaktif</option>
                <option value="tutup"    {{ request('status')==='tutup' ? 'selected':'' }}>Tutup</option>
            </select>

            <button type="submit"
                class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow hover:shadow-md transition">
                Filter
            </button>
        </form>

        {{-- Button --}}
        <a href="{{ route('admin.pelanggan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition">
            + Tambah Pelanggan
        </a>

    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            {{-- Head --}}
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase tracking-wider text-gray-500">
                    <th class="px-5 py-3 text-left">No / Nama</th>
                    <th class="px-3 py-3 text-left">Alamat</th>
                    <th class="px-3 py-3 text-center">No. HP</th>
                    <th class="px-3 py-3 text-center">Meteran</th>
                    <th class="px-3 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            {{-- Body --}}
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pelanggan as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                    {{-- Nama --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($p->nama_pelanggan,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $p->nama_pelanggan }}
                                </p>
                                <p class="font-mono text-xs text-brand-600 dark:text-brand-400">
                                    {{ $p->nomor_pelanggan }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Alamat --}}
                    <td class="px-3 py-3 text-gray-600 dark:text-gray-400 text-xs max-w-[180px] truncate">
                        {{ $p->alamat }}
                    </td>

                    {{-- HP --}}
                    <td class="px-3 py-3 text-center text-xs text-gray-500">
                        {{ $p->no_hp ?? '-' }}
                    </td>

                    {{-- Meter --}}
                    <td class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                        {{ number_format($p->meteran_awal) }}
                    </td>

                    {{-- Status --}}
                    <td class="px-3 py-3 text-center">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                            {{ $p->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                            : ($p->status==='nonaktif' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300') }}">
                            {{ $p->status }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">

                            <a href="{{ route('admin.pelanggan.show', $p) }}"
                                class="p-2 rounded-lg text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/30 transition"
                                title="Detail">
                                👁
                            </a>

                            <a href="{{ route('admin.pelanggan.edit', $p) }}"
                                class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition"
                                title="Edit">
                                ✏
                            </a>

                            <form method="POST" action="{{ route('admin.pelanggan.destroy', $p) }}"
                                onsubmit="return confirm('Hapus pelanggan {{ $p->nama_pelanggan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition"
                                    title="Hapus">
                                    🗑
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                        Tidak ada pelanggan
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- Pagination --}}
    @if($pelanggan->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            {{ $pelanggan->firstItem() }}–{{ $pelanggan->lastItem() }} dari {{ $pelanggan->total() }}
        </p>

        <div class="flex gap-1">
            @if(!$pelanggan->onFirstPage())
            <a href="{{ $pelanggan->previousPageUrl() }}"
                class="px-3 py-1.5 text-xs rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                ‹
            </a>
            @endif

            @if($pelanggan->hasMorePages())
            <a href="{{ $pelanggan->nextPageUrl() }}"
                class="px-3 py-1.5 text-xs rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                ›
            </a>
            @endif
        </div>
    </div>
    @endif

</div>

@endsection