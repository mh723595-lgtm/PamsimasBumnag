{{-- resources/views/pelanggan/pengaduan/create.blade.php --}}
@extends('layouts.app')
@section('title','Buat Pengaduan')
@section('page_title','Buat Pengaduan Baru')
@section('page_subtitle','Laporkan masalah atau keluhan terkait layanan air')

@section('content')
<div class="mb-4">
    <a href="{{ route('pelanggan.pengaduan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <form method="POST" action="{{ route('pelanggan.pengaduan.store') }}" enctype="multipart/form-data"
              x-data="{ preview: null, fileName: '' }">
            @csrf

            {{-- Jenis --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jenis Pengaduan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach(['kerusakan'=>['🔧','Kerusakan'],'tagihan'=>['💰','Tagihan'],'pelayanan'=>['🙋','Pelayanan'],'lainnya'=>['📋','Lainnya']] as $val=>[$ico,$label])
                    <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer text-xs font-semibold transition-all
                        {{ old('jenis')===$val ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20 text-brand-700' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300' }}">
                        <input type="radio" name="jenis" value="{{ $val }}" {{ old('jenis')===$val ? 'checked' : '' }} class="sr-only">
                        <span class="text-2xl">{{ $ico }}</span>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('jenis')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Judul --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Pengaduan <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul') }}" required
                    placeholder="Contoh: Air tidak mengalir sejak 2 hari lalu"
                    class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                @error('judul')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" rows="5" required
                    placeholder="Ceritakan masalah Anda secara detail: kapan terjadi, lokasi, dampaknya..."
                    class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Upload Foto --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Foto Pendukung <span class="text-gray-400 font-normal text-xs">(opsional, maks 2MB)</span>
                </label>
                <label class="relative flex flex-col items-center justify-center w-full h-36 bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all overflow-hidden">
                    <template x-if="!preview">
                        <div class="text-center">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-300 dark:text-gray-600 mt-0.5">JPG, PNG, WEBP maks 2MB</p>
                        </div>
                    </template>
                    <template x-if="preview">
                        <div class="relative w-full h-full">
                            <img :src="preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <p class="text-white text-xs font-semibold">Klik untuk ganti foto</p>
                            </div>
                        </div>
                    </template>
                    <input type="file" name="foto" accept="image/*" class="sr-only"
                        @change="
                            const f = $event.target.files[0];
                            if (f) {
                                fileName = f.name;
                                const reader = new FileReader();
                                reader.onload = e => preview = e.target.result;
                                reader.readAsDataURL(f);
                            }
                        ">
                </label>
                <p x-show="fileName" x-text="'📎 ' + fileName" class="text-xs text-gray-400 mt-1"></p>
                @error('foto')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>
@endsection
