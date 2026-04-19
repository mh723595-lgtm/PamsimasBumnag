@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Konfigurasi umum sistem PAMSIMAS')

@section('content')

<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
        @csrf @method('PUT')

        <div class="space-y-4">
            @foreach($settings as $grup => $items)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm capitalize">
                        @if($grup === 'umum') ⚙️ Pengaturan Umum
                        @elseif($grup === 'tarif') 💰 Pengaturan Tarif
                        @elseif($grup === 'tagihan') 📋 Pengaturan Tagihan
                        @else {{ ucfirst($grup) }}
                        @endif
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @foreach($items as $s)
                    <div class="grid grid-cols-3 gap-4 items-start">
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $s->label ?? $s->key }}</label>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $s->key }}</p>
                        </div>
                        <div class="col-span-2">
                            @if($s->tipe === 'textarea')
                            <textarea name="{{ $s->key }}" rows="3"
                                class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ $s->value }}</textarea>
                            @elseif($s->tipe === 'boolean')
                            <div class="flex items-center gap-3 mt-1">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $s->key }}" value="1" {{ $s->value ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600"></div>
                                </label>
                            </div>
                            @elseif($s->tipe === 'number')
                            <div class="relative">
                                <input type="number" name="{{ $s->key }}" value="{{ $s->value }}"
                                    class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                @if(str_contains($s->key, 'tarif') || str_contains($s->key, 'biaya'))
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                @endif
                            </div>
                            @else
                            <input type="text" name="{{ $s->key }}" value="{{ $s->value }}"
                                class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
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
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@endsection 