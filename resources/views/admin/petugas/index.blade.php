{{-- resources/views/admin/petugas/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Petugas')
@section('page_title','Manajemen Petugas')
@section('page_subtitle','Kelola data petugas lapangan')

@section('content')
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex gap-3 items-end">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Cari</button>
        </form>
        <a href="{{ route('admin.petugas.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Petugas
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 dark:bg-gray-800/60">
                <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas</th>
                <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">NIK / Jabatan</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($petugas as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($p->nama_petugas,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_petugas }}</p>
                                <p class="text-xs text-gray-400">{{ $p->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $p->nik ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $p->jabatan ?? '-' }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-500">{{ $p->no_hp ?? '-' }}</td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }} capitalize">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.petugas.edit', $p) }}" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.petugas.destroy', $p) }}" onsubmit="return confirm('Hapus petugas {{ $p->nama_petugas }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-16 text-center text-gray-400">Tidak ada petugas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($petugas->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">{{ $petugas->links() }}</div>
    @endif
</div>
@endsection