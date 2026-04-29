{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Pengguna')
@section('page_title','Manajemen Pengguna')
@section('page_subtitle','Kelola semua akun pengguna sistem')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([['Total',($stats['total']),'gray'],['Admin',$stats['admin'],'purple'],['Petugas',$stats['petugas'],'blue'],['Pelanggan',$stats['pelanggan'],'emerald']] as [$lbl,$val,$color])
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">{{ $lbl }}</p>
        <p class="text-xl font-extrabold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $val }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    {{-- Toolbar --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select name="role" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                <option value="">Semua Role</option>
                <option value="admin"     {{ request('role')==='admin'     ? 'selected':'' }}>Admin</option>
                <option value="petugas"   {{ request('role')==='petugas'   ? 'selected':'' }}>Petugas</option>
                <option value="pelanggan" {{ request('role')==='pelanggan' ? 'selected':'' }}>Pelanggan</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Filter</button>
        </form>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $u->name }}</p>
                        </div>
                    </td>
                    <td class="px-3 py-3.5 text-gray-500 dark:text-gray-400 text-sm">{{ $u->email }}</td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                            {{ $u->role==='admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'
                                : ($u->role==='petugas' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                                : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300') }}">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $u->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-green-500' : 'bg-red-500' }} inline-block"></span>
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-right text-xs text-gray-400">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.users.edit', $u) }}"
                                class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                onsubmit="return confirm('Yakin hapus user {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">Tidak ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">{{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}</p>
        <div class="flex gap-1">
            @if(!$users->onFirstPage())<a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>@endif
            @if($users->hasMorePages())<a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>@endif
        </div>
    </div>
    @endif
</div>
@endsection
