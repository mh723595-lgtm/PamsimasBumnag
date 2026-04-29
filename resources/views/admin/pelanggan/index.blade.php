{{-- resources/views/admin/pelanggan/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Pelanggan')
@section('page_title','Manajemen Pelanggan')
@section('page_subtitle','Kelola data semua pelanggan')

@section('content')
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([['Total',$stats['total'],'blue'],['Aktif',$stats['aktif'],'emerald'],['Nonaktif',$stats['nonaktif'],'red']] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">{{ $l }}</p>
        <p class="text-xl font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $v }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / no. pelanggan / HP..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="status" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Nonaktif</option>
                <option value="tutup"    {{ request('status')==='tutup'    ? 'selected':'' }}>Tutup</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Filter</button>
        </form>
        <a href="{{ route('admin.pelanggan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pelanggan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. / Nama</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Meteran Awal</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pelanggan as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($p->nama_pelanggan,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_pelanggan }}</p>
                                <p class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $p->nomor_pelanggan }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5 text-gray-600 dark:text-gray-400 text-xs max-w-[160px] truncate">{{ $p->alamat }}</td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-500">{{ $p->no_hp ?? '-' }}</td>
                    <td class="px-3 py-3.5 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($p->meteran_awal) }}</td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $p->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                                : ($p->status==='nonaktif' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                                : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300') }} capitalize">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.pelanggan.show', $p) }}" class="p-1.5 rounded-lg text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.pelanggan.edit', $p) }}" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.pelanggan.destroy', $p) }}" onsubmit="return confirm('Hapus pelanggan {{ $p->nama_pelanggan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">Tidak ada pelanggan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pelanggan->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">{{ $pelanggan->firstItem() }}–{{ $pelanggan->lastItem() }} dari {{ $pelanggan->total() }}</p>
        <div class="flex gap-1">
            @if(!$pelanggan->onFirstPage())<a href="{{ $pelanggan->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>@endif
            @if($pelanggan->hasMorePages())<a href="{{ $pelanggan->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>@endif
        </div>
    </div>
    @endif
</div>
@endsection
