{{-- resources/views/admin/laporan/index.blade.php --}}
@extends('layouts.app')
@section('title','Laporan')
@section('page_title','Laporan')
@section('page_subtitle','Export dan cetak laporan sistem PAMSIMAS')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @php
    $cards = [
        ['title'=>'Laporan Tagihan','desc'=>'Daftar tagihan air per periode','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','color'=>'blue','route'=>'admin.laporan.tagihan'],
        ['title'=>'Laporan Pembayaran','desc'=>'Rekapitulasi pembayaran pelanggan','icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','color'=>'emerald','route'=>'admin.laporan.pembayaran'],
        ['title'=>'Laporan Pemakaian','desc'=>'Volume pemakaian air per periode','icon'=>'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z','color'=>'teal','route'=>'admin.laporan.pemakaian'],
    ];
    @endphp
    @foreach($cards as $c)
    <a href="{{ route($c['route']) }}"
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:border-{{ $c['color'] }}-200 dark:hover:border-{{ $c['color'] }}-700 transition-all p-6 group">
        <div class="w-12 h-12 rounded-xl bg-{{ $c['color'] }}-50 dark:bg-{{ $c['color'] }}-900/30 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6 text-{{ $c['color'] }}-600 dark:text-{{ $c['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
            </svg>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white mb-1">{{ $c['title'] }}</h3>
        <p class="text-sm text-gray-400">{{ $c['desc'] }}</p>
        <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-{{ $c['color'] }}-600 dark:text-{{ $c['color'] }}-400">
            <span>Lihat Laporan</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </a>
    @endforeach
</div>
@endsection