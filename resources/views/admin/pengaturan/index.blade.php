@extends('layouts.app')
@section('title','Pengaturan Sistem')
@section('page_title','Pengaturan Sistem')
@section('page_subtitle','Konfigurasi aplikasi PAMSIMAS — Tarif, Denda, Notifikasi')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Form Pengaturan --}}
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('admin.pengaturan.update') }}">
            @csrf @method('PUT')

            @php
            $grupLabel = [
                'umum'       => ['icon'=>'⚙️','label'=>'Informasi Sistem'],
                'tarif'      => ['icon'=>'💰','label'=>'Tarif Air'],
                'tagihan'    => ['icon'=>'📋','label'=>'Pengaturan Tagihan'],
                'denda'      => ['icon'=>'⚠️','label'=>'Denda Keterlambatan'],
                'notifikasi' => ['icon'=>'🔔','label'=>'Notifikasi Otomatis'],
            ];
            @endphp

            <div class="space-y-4">
                @foreach($settings as $grup => $items)
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 flex items-center gap-2">
                        <span class="text-base">{{ $grupLabel[$grup]['icon'] ?? '🔧' }}</span>
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm">{{ $grupLabel[$grup]['label'] ?? ucfirst($grup) }}</h3>
                        @if($grup === 'denda')
                        <span class="ml-auto px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs rounded-full font-semibold">
                            {{ \App\Models\SettingAplikasi::get('denda_aktif','1') ? '🟢 Aktif' : '🔴 Nonaktif' }}
                        </span>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($items as $s)
                        <div class="grid grid-cols-5 gap-4 items-center px-5 py-3.5">
                            <div class="col-span-2">
                                <label for="s_{{ $s->key }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $s->label ?? $s->key }}</label>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $s->key }}</p>
                            </div>
                            <div class="col-span-3">
                                @if($s->tipe === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer" x-data="{ on: {{ $s->value ? 'true' : 'false' }} }">
                                    <input type="hidden" name="{{ $s->key }}" value="0">
                                    <input type="checkbox" id="s_{{ $s->key }}" name="{{ $s->key }}" value="1"
                                        @if($s->value) checked @endif
                                        x-model="on"
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600 transition-all relative"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300" x-text="on ? 'Aktif' : 'Nonaktif'">{{ $s->value ? 'Aktif' : 'Nonaktif' }}</span>
                                </label>

                                @elseif($s->tipe === 'textarea')
                                <textarea id="s_{{ $s->key }}" name="{{ $s->key }}" rows="3"
                                    class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ $s->value }}</textarea>

                                @elseif($s->tipe === 'number')
                                <div class="relative">
                                    <input type="number" id="s_{{ $s->key }}" name="{{ $s->key }}"
                                        value="{{ $s->value }}" min="0"
                                        class="w-full py-2.5 px-4 pr-12 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                    @if(str_contains($s->key,'tarif') || str_contains($s->key,'biaya') || str_contains($s->key,'minimum'))
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">Rp</span>
                                    @elseif(str_contains($s->key,'persen') || str_contains($s->key,'maksimum'))
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">%</span>
                                    @elseif(str_contains($s->key,'hari') || str_contains($s->key,'period') || str_contains($s->key,'h_minus'))
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">hr</span>
                                    @endif
                                </div>
                                @error($s->key)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

                                @else
                                <input type="text" id="s_{{ $s->key }}" name="{{ $s->key }}"
                                    value="{{ $s->value }}"
                                    class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                @error($s->key)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 flex gap-3">
                <button type="submit"
                    class="px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Semua Pengaturan
                </button>
                <a href="{{ route('admin.dashboard') }}"
                    class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Sidebar Kanan --}}
    <div class="space-y-4">

        {{-- Preview Tarif --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">💡 Preview Kalkulasi Tarif</h3>
            @php
            $t1 = (int) \App\Models\SettingAplikasi::get('tarif_blok1', 20000);
            $t2 = (int) \App\Models\SettingAplikasi::get('tarif_blok2', 1500);
            $t3 = (int) \App\Models\SettingAplikasi::get('tarif_blok3', 2000);
            $adm = (int) \App\Models\SettingAplikasi::get('biaya_admin', 2500);
            $contoh = [8, 15, 25, 35];
            @endphp
            <div class="space-y-2">
                @foreach($contoh as $p)
                @php
                $t = $t1;
                if($p > 10) $t += (min($p,20)-10)*$t2;
                if($p > 20) $t += ($p-20)*$t3;
                $t += $adm;
                @endphp
                <div class="flex justify-between items-center py-2 px-3 bg-brand-50 dark:bg-brand-900/20 rounded-xl border border-brand-100 dark:border-brand-900">
                    <span class="text-xs text-brand-600 dark:text-brand-400 font-medium">{{ $p }} m³</span>
                    <span class="font-bold text-brand-800 dark:text-brand-200 text-sm">Rp {{ number_format($t,0,',','.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Proses Denda Manual --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-800 shadow-sm p-5">
            <h3 class="font-bold text-amber-800 dark:text-amber-200 text-sm mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Proses Denda Manual
            </h3>
            <p class="text-xs text-amber-600 dark:text-amber-400 mb-4 leading-relaxed">
                Denda diproses otomatis setiap hari jam 00:30 via scheduler.
                Gunakan tombol ini untuk memproses denda secara manual sekarang.
            </p>
            <form method="POST" action="{{ route('admin.pengaturan.proses-denda') }}">
                @csrf
                <button type="submit"
                    class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all text-sm flex items-center justify-center gap-2"
                    onclick="return confirm('Proses denda untuk semua tagihan terlambat sekarang?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Proses Denda Sekarang
                </button>
            </form>
        </div>

        {{-- Info Denda --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">📌 Cara Kerja Denda</h3>
            @php
            $persen = (int) \App\Models\SettingAplikasi::get('denda_persen', 2);
            $grace  = (int) \App\Models\SettingAplikasi::get('denda_grace_period', 0);
            $min    = (int) \App\Models\SettingAplikasi::get('denda_minimum', 2000);
            $max    = (int) \App\Models\SettingAplikasi::get('denda_maksimum_persen', 50);
            @endphp
            <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                <div class="flex gap-2"><span class="text-amber-500">▶</span><span>Denda dikenakan setelah melewati jatuh tempo + grace period <strong>{{ $grace }} hari</strong></span></div>
                <div class="flex gap-2"><span class="text-amber-500">▶</span><span>Besar denda: <strong>{{ $persen }}%/bulan</strong> × tagihan pokok</span></div>
                <div class="flex gap-2"><span class="text-amber-500">▶</span><span>Minimum denda: <strong>Rp {{ number_format($min,0,',','.') }}</strong></span></div>
                <div class="flex gap-2"><span class="text-amber-500">▶</span><span>Maksimum denda: <strong>{{ $max }}%</strong> dari tagihan</span></div>
                <div class="flex gap-2"><span class="text-brand-500">▶</span><span>Notifikasi dikirim otomatis ke pelanggan saat denda dikenakan</span></div>
            </div>
        </div>
    </div>
</div>

@endsection
