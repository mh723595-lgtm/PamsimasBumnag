@extends('layouts.app')

@section('title', 'Manajemen Pengaduan')
@section('page_title', 'Manajemen Pengaduan')
@section('page_subtitle', 'Kelola semua pengaduan pelanggan')

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $statItems = [
        ['label'=>'Pengaduan Baru',    'val'=>$stats['baru'],     'color'=>'blue',   'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['label'=>'Sedang Diproses',   'val'=>$stats['diproses'], 'color'=>'amber',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Selesai',           'val'=>$stats['selesai'],  'color'=>'emerald','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Ditolak',           'val'=>$stats['ditolak'],  'color'=>'red',    'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    @endphp
    @foreach($statItems as $s)
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl bg-{{ $s['color'] }}-50 dark:bg-{{ $s['color'] }}-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <p class="text-xs text-gray-400">{{ $s['label'] }}</p>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $s['val'] }}</p>
    </div>
    @endforeach
</div>

{{-- TABLE --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    {{-- Filter --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul / no. pengaduan / nama pelanggan..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="baru"     {{ request('status')==='baru'     ? 'selected':'' }}>Baru</option>
                    <option value="diproses" {{ request('status')==='diproses' ? 'selected':'' }}>Diproses</option>
                    <option value="selesai"  {{ request('status')==='selesai'  ? 'selected':'' }}>Selesai</option>
                    <option value="ditolak"  {{ request('status')==='ditolak'  ? 'selected':'' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Prioritas</label>
                <select name="prioritas" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Semua</option>
                    <option value="tinggi" {{ request('prioritas')==='tinggi' ? 'selected':'' }}>🔴 Tinggi</option>
                    <option value="sedang" {{ request('prioritas')==='sedang' ? 'selected':'' }}>🟡 Sedang</option>
                    <option value="rendah" {{ request('prioritas')==='rendah' ? 'selected':'' }}>🟢 Rendah</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['search','status','jenis','prioritas']))
            <a href="{{ route('admin.pengaduan.index') }}" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. / Judul</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pengaduan as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors {{ $p->status === 'baru' ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">
                    <td class="px-5 py-3.5">
                        <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-0.5">{{ $p->nomor_pengaduan }}</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ Str::limit($p->judul, 40) }}</p>
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="font-medium text-gray-700 dark:text-gray-300 text-sm">{{ $p->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $p->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 capitalize">
                            {{ $p->jenis }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-center text-lg">
                        {{ $p->prioritas === 'tinggi' ? '🔴' : ($p->prioritas === 'sedang' ? '🟡' : '🟢') }}
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->statusBadge() }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5 text-right text-xs text-gray-400">
                        {{ $p->created_at->diffForHumans() }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <a href="{{ route('admin.pengaduan.show', $p) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-600 bg-brand-50 dark:bg-brand-900/30 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-lg transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Tangani
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <svg class="w-14 h-14 text-gray-200 dark:text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <p class="text-gray-400 font-medium">Tidak ada pengaduan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengaduan->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-400">{{ $pengaduan->firstItem() }}–{{ $pengaduan->lastItem() }} dari {{ $pengaduan->total() }}</p>
        <div class="flex gap-1">
            @if(!$pengaduan->onFirstPage())
            <a href="{{ $pengaduan->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>
            @endif
            @if($pengaduan->hasMorePages())
            <a href="{{ $pengaduan->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection