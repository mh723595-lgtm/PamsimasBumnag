{{-- resources/views/pelanggan/pengaduan/index.blade.php --}}
@extends('layouts.app')
@section('title','Pengaduan Saya')
@section('page_title','Pengaduan Saya')
@section('page_subtitle','Riwayat semua pengaduan yang pernah Anda buat')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500 dark:text-gray-400">Total: <strong class="text-gray-800 dark:text-white">{{ $pengaduan->total() }}</strong> pengaduan</p>
    <a href="{{ route('pelanggan.pengaduan.create') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Pengaduan
    </a>
</div>

<div class="space-y-3">
    @forelse($pengaduan as $p)
    <a href="{{ route('pelanggan.pengaduan.show', $p) }}"
        class="block bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-brand-200 dark:hover:border-brand-700 transition-all p-5 group">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $p->nomor_pengaduan }}</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 capitalize">{{ $p->jenis }}</span>
                </div>
                <p class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $p->judul }}</p>
                <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $p->deskripsi }}</p>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-2">{{ $p->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->statusBadge() }}">
                    {{ ucfirst($p->status) }}
                </span>
                <span class="text-lg">{{ $p->prioritas === 'tinggi' ? '🔴' : ($p->prioritas === 'sedang' ? '🟡' : '🟢') }}</span>
            </div>
        </div>
        @if($p->tanggapan)
        <div class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-800">
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mb-0.5">Tanggapan Admin:</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">{{ $p->tanggapan }}</p>
        </div>
        @endif
    </a>
    @empty
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm py-20 text-center">
        <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <p class="text-gray-400 font-medium mb-2">Belum ada pengaduan</p>
        <a href="{{ route('pelanggan.pengaduan.create') }}" class="text-brand-600 text-sm hover:underline">Buat pengaduan pertama Anda →</a>
    </div>
    @endforelse
</div>
@if($pengaduan->hasPages())
<div class="mt-4">{{ $pengaduan->links() }}</div>
@endif
@endsection