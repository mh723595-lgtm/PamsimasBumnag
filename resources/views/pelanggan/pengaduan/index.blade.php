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

                {{-- Mini Timeline --}}
                <div class="flex items-center gap-0 mt-3">
                    @php
                        $steps = ['baru' => 1, 'diproses' => 2, 'selesai' => 3, 'ditolak' => 3];
                        $currentStep = $steps[$p->status] ?? 1;
                        $isDitolak = $p->status === 'ditolak';
                    @endphp

                    {{-- Step 1: Dibuat --}}
                    <div class="flex flex-col items-center">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">✓</div>
                        <span class="text-[9px] text-gray-400 mt-0.5 whitespace-nowrap">Dibuat</span>
                    </div>

                    <div class="h-px w-6 mb-3 {{ $currentStep >= 2 ? 'bg-green-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>

                    {{-- Step 2: Ditanggapi --}}
                    <div class="flex flex-col items-center">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold
                            {{ $currentStep >= 2 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                            {{ $currentStep >= 2 ? '✓' : '2' }}
                        </div>
                        <span class="text-[9px] text-gray-400 mt-0.5 whitespace-nowrap">Ditanggapi</span>
                    </div>

                    <div class="h-px w-6 mb-3 {{ $currentStep >= 3 && !$isDitolak ? 'bg-green-400' : ($isDitolak ? 'bg-red-300' : 'bg-gray-200 dark:bg-gray-700') }}"></div>

                    {{-- Step 3: Selesai / Ditolak --}}
                    <div class="flex flex-col items-center">
                        @if($isDitolak)
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">✕</div>
                            <span class="text-[9px] text-red-400 mt-0.5 whitespace-nowrap">Ditolak</span>
                        @else
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold
                                {{ $currentStep >= 3 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                {{ $currentStep >= 3 ? '✓' : '3' }}
                            </div>
                            <span class="text-[9px] text-gray-400 mt-0.5 whitespace-nowrap">Selesai</span>
                        @endif
                    </div>
                </div>

                <p class="text-xs text-gray-300 dark:text-gray-600 mt-2">{{ $p->created_at->diffForHumans() }}</p>
            </div>

            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                {{-- Badge Status — Vivid --}}
                @php
                    $badgeConfig = match($p->status) {
                        'baru'     => ['dot' => '#FFC107', 'bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Baru'],
                        'diproses' => ['dot' => '#007BFF', 'bg' => '#CCE5FF', 'color' => '#004085', 'label' => 'Diproses'],
                        'selesai'  => ['dot' => '#28A745', 'bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Selesai'],
                        'ditolak'  => ['dot' => '#DC3545', 'bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak'],
                        default    => ['dot' => '#6C757D', 'bg' => '#E2E3E5', 'color' => '#383D41', 'label' => ucfirst($p->status)],
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                    style="background-color: {{ $badgeConfig['bg'] }}; color: {{ $badgeConfig['color'] }};">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $badgeConfig['dot'] }};"></span>
                    {{ $badgeConfig['label'] }}
                </span>
                <span class="text-lg">{{ $p->prioritasBadge() }}</span>
            </div>
        </div>

        @if($p->tanggapan)
        <div class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-800">
            <p class="text-xs font-semibold mb-0.5" style="color: #155724;">Tanggapan Admin:</p>
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