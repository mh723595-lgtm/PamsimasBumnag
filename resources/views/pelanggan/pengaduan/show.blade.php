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

        {{-- Header --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            @php
                $badgeConfig = match($pengaduan->status) {
                    'baru'     => ['dot' => '#FFC107', 'bg' => '#FFF3CD', 'color' => '#856404', 'label' => 'Baru'],
                    'diproses' => ['dot' => '#007BFF', 'bg' => '#CCE5FF', 'color' => '#004085', 'label' => 'Diproses'],
                    'selesai'  => ['dot' => '#28A745', 'bg' => '#D4EDDA', 'color' => '#155724', 'label' => 'Selesai'],
                    'ditolak'  => ['dot' => '#DC3545', 'bg' => '#F8D7DA', 'color' => '#721C24', 'label' => 'Ditolak'],
                    default    => ['dot' => '#6C757D', 'bg' => '#E2E3E5', 'color' => '#383D41', 'label' => ucfirst($pengaduan->status)],
                };
            @endphp
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-1">{{ $pengaduan->nomor_pengaduan }}</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $pengaduan->judul }}</h2>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-bold"
                    style="background-color: {{ $badgeConfig['bg'] }}; color: {{ $badgeConfig['color'] }};">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $badgeConfig['dot'] }};"></span>
                    {{ $badgeConfig['label'] }}
                </span>
            </div>
            <div class="flex gap-2 mt-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">{{ $pengaduan->jenis }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    {{ $pengaduan->prioritasBadge() }} {{ ucfirst($pengaduan->prioritas) }}
                </span>
                <span class="text-xs text-gray-400 flex items-center">{{ $pengaduan->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Progress Timeline --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Progress Pengaduan</h3>
            @php
                $steps = ['baru' => 1, 'diproses' => 2, 'selesai' => 3, 'ditolak' => 3];
                $currentStep = $steps[$pengaduan->status] ?? 1;
                $isDitolak = $pengaduan->status === 'ditolak';

                $timelineSteps = [
                    ['label' => 'Dibuat',     'sub' => $pengaduan->created_at->format('d M Y'),  'step' => 1],
                    ['label' => 'Ditanggapi', 'sub' => $pengaduan->tanggapan ? 'Sudah ditanggapi' : 'Menunggu', 'step' => 2],
                    ['label' => 'Diproses',   'sub' => $currentStep >= 2 ? 'Sedang ditangani' : 'Menunggu', 'step' => 2],
                    ['label' => 'Selesai',    'sub' => $pengaduan->tanggal_selesai ? $pengaduan->tanggal_selesai->format('d M Y') : 'Menunggu', 'step' => 3],
                ];
            @endphp

            <div class="flex items-start">
                @foreach($timelineSteps as $i => $step)
                    <div class="flex flex-col items-center flex-1">
                        @if($isDitolak && $i === 3)
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2"
                                style="background:#F8D7DA; color:#721C24; border-color:#DC3545;">✕</div>
                        @elseif($currentStep > $step['step'] || ($currentStep === $step['step'] && $i < 2))
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2"
                                style="background:#D4EDDA; color:#155724; border-color:#28A745;">✓</div>
                        @elseif($currentStep === $step['step'])
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2"
                                style="background:#CCE5FF; color:#004085; border-color:#007BFF;">→</div>
                        @else
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-400 border-2 border-gray-200 dark:border-gray-700">{{ $i + 1 }}</div>
                        @endif

                        <p class="text-xs font-semibold mt-1.5 text-center"
                            style="color: {{ $isDitolak && $i === 3 ? '#721C24' : ($currentStep > $step['step'] || ($currentStep === $step['step'] && $i < 2) ? '#155724' : ($currentStep === $step['step'] ? '#004085' : '#9CA3AF')) }}">
                            {{ $isDitolak && $i === 3 ? 'Ditolak' : $step['label'] }}
                        </p>
                        <p class="text-[10px] text-gray-400 text-center mt-0.5 px-1">{{ $step['sub'] }}</p>
                    </div>

                    @if($i < count($timelineSteps) - 1)
                        <div class="h-px flex-1 mt-4"
                            style="background: {{ $isDitolak && $i === 2 ? '#DC3545' : ($currentStep > $step['step'] ? '#28A745' : '#E5E7EB') }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h3>
            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $pengaduan->deskripsi }}</p>
        </div>

        {{-- Foto --}}
        @if($pengaduan->foto)
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Foto</h3>
            <img src="{{ Storage::url($pengaduan->foto) }}" class="max-h-52 rounded-xl object-cover border border-gray-100 dark:border-gray-800 shadow-sm">
        </div>
        @endif

        {{-- Tanggapan Admin --}}
        @if($pengaduan->tanggapan)
        <div class="p-5" style="background:#D4EDDA;">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#28A745;">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold" style="color:#155724;">Tanggapan Admin</p>
            </div>
            <p class="text-sm leading-relaxed" style="color:#155724;">{{ $pengaduan->tanggapan }}</p>
            @if($pengaduan->tanggal_selesai)
            <p class="text-xs mt-2" style="color:#1e7e34;">{{ $pengaduan->tanggal_selesai->format('d M Y H:i') }}</p>
            @endif
        </div>
        @else
        <div class="p-5" style="background:#FFF3CD;">
            <p class="text-sm font-medium" style="color:#856404;">
                ⏳ Pengaduan Anda sedang dalam proses penanganan. Kami akan segera menghubungi Anda.
            </p>
        </div>
        @endif

    </div>
</div>
@endsection