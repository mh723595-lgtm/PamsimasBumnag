@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')
@section('page_subtitle', 'Semua notifikasi sistem')

@section('content')

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        <span class="font-semibold text-gray-800 dark:text-white">{{ $notifikasi->total() }}</span> notifikasi
    </p>
    @if(auth()->user()->notifBelumDibaca() > 0)
    <form method="POST" action="{{ route('admin.notifikasi.baca-semua') }}">
        @csrf
        <button type="submit"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/30 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-xl transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Tandai Semua Dibaca
        </button>
    </form>
    @endif
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="divide-y divide-gray-50 dark:divide-gray-800">
        @forelse($notifikasi as $n)
        <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$n->sudah_dibaca ? 'bg-brand-50/40 dark:bg-brand-900/10' : '' }}">
            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $n->tipe === 'success' ? 'bg-emerald-100 dark:bg-emerald-900/40'
                    : ($n->tipe === 'warning' ? 'bg-amber-100 dark:bg-amber-900/40'
                    : ($n->tipe === 'error' ? 'bg-red-100 dark:bg-red-900/40'
                    : 'bg-brand-100 dark:bg-brand-900/40')) }}">
                @if($n->tipe === 'success')
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @elseif($n->tipe === 'warning')
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                @elseif($n->tipe === 'error')
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @else
                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm {{ !$n->sudah_dibaca ? 'font-bold' : '' }}">
                            {{ $n->judul }}
                            @if(!$n->sudah_dibaca)
                            <span class="inline-block w-2 h-2 rounded-full bg-brand-500 ml-1 align-middle"></span>
                            @endif
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 leading-relaxed">{{ $n->pesan }}</p>
                    </div>
                    @if(!$n->sudah_dibaca)
                    <form method="POST" action="{{ route('admin.notifikasi.baca', $n) }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="text-xs text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors whitespace-nowrap">Tandai dibaca</button>
                    </form>
                    @endif
                </div>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1.5">{{ $n->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="py-20 text-center">
            <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-400 font-medium">Tidak ada notifikasi</p>
        </div>
        @endforelse
    </div>

    @if($notifikasi->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $notifikasi->links() }}
    </div>
    @endif
</div>

@endsection