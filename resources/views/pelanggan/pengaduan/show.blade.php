{{-- resources/views/pelanggan/pengaduan/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pengaduan')
@section('page_title','Detail Pengaduan')
@section('page_subtitle',$pengaduan->nomor_pengaduan)

@section('content')
<div class="mb-4">
    <a href="{{ route('pelanggan.pengaduan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="max-w-2xl space-y-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-1">{{ $pengaduan->nomor_pengaduan }}</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $pengaduan->judul }}</h2>
                </div>
                <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $pengaduan->statusBadge() }}">
                    {{ ucfirst($pengaduan->status) }}
                </span>
            </div>
            <div class="flex gap-2 mt-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">{{ $pengaduan->jenis }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    {{ $pengaduan->prioritas === 'tinggi' ? '🔴' : ($pengaduan->prioritas === 'sedang' ? '🟡' : '🟢') }} {{ ucfirst($pengaduan->prioritas) }}
                </span>
                <span class="text-xs text-gray-400 flex items-center">{{ $pengaduan->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h3>
            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $pengaduan->deskripsi }}</p>
        </div>

        @if($pengaduan->foto)
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Foto</h3>
            <img src="{{ Storage::url($pengaduan->foto) }}" class="max-h-52 rounded-xl object-cover border border-gray-100 dark:border-gray-800 shadow-sm">
        </div>
        @endif

        @if($pengaduan->tanggapan)
        <div class="p-5 bg-emerald-50 dark:bg-emerald-900/10">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200">Tanggapan Admin</p>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $pengaduan->tanggapan }}</p>
            @if($pengaduan->tanggal_selesai)
            <p class="text-xs text-gray-400 mt-2">{{ $pengaduan->tanggal_selesai->format('d M Y H:i') }}</p>
            @endif
        </div>
        @else
        <div class="p-5 bg-amber-50 dark:bg-amber-900/10">
            <p class="text-sm text-amber-700 dark:text-amber-300 font-medium">
                ⏳ Pengaduan Anda sedang dalam proses penanganan. Kami akan segera menghubungi Anda.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection