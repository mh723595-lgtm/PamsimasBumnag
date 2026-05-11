@extends('layouts.app')
@section('title','Buat Pengaduan')
@section('page_title','Buat Pengaduan Baru')
@section('page_subtitle','Laporkan masalah atau keluhan kepada admin PAMSIMAS')

@section('content')
<div class="max-w-xl">
    <div class="mb-4">
        <a href="{{ route('pelanggan.pengaduan.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Pengaduan
        </a>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h2 class="font-bold text-gray-800 dark:text-white mb-1">Form Pengaduan</h2>
        <p class="text-gray-400 text-sm mb-5">Isi form berikut dengan lengkap agar pengaduan dapat segera diproses.</p>

        <form method="POST" action="{{ route('pelanggan.pengaduan.store') }}" enctype="multipart/form-data"
            class="space-y-4">
            @csrf

            {{-- JENIS — select native, bisa dipilih di HP --}}
            <div>
                <label for="jenis" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Jenis Pengaduan <span class="text-red-500">*</span>
                </label>
                <select id="jenis" name="jenis" required
                    class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:border-brand-500 focus:ring-0 transition-all appearance-none">
                    <option value="">-- Pilih jenis pengaduan --</option>
                    <option value="kerusakan" {{ old('jenis')==='kerusakan' ? 'selected':'' }}>🔧 Kerusakan Instalasi / Pipa</option>
                    <option value="tagihan"   {{ old('jenis')==='tagihan'   ? 'selected':'' }}>💰 Masalah Tagihan / Pembayaran</option>
                    <option value="pelayanan" {{ old('jenis')==='pelayanan' ? 'selected':'' }}>🙋 Keluhan Pelayanan</option>
                    <option value="lainnya"   {{ old('jenis')==='lainnya'   ? 'selected':'' }}>📋 Lainnya</option>
                </select>
                @error('jenis')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Judul --}}
            <div>
                <label for="judul" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Judul Pengaduan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="judul" name="judul"
                    value="{{ old('judul') }}"
                    maxlength="200" required
                    placeholder="Contoh: Pipa bocor di RT 03, air tidak mengalir..."
                    class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-0 transition-all">
                @error('judul')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required
                    placeholder="Jelaskan masalah secara detail: lokasi, kapan terjadi, dampak yang dirasakan..."
                    class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-0 resize-none transition-all">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Foto Bukti --}}
            <div x-data="{ preview: null, filename: '' }">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Foto Bukti
                    <span class="text-gray-400 font-normal text-xs">(opsional, maks 2MB)</span>
                </label>

                {{-- Preview foto --}}
                <template x-if="preview">
                    <div class="relative mb-3 rounded-xl overflow-hidden border-2 border-brand-300 dark:border-brand-700">
                        <img :src="preview" class="w-full max-h-48 object-cover">
                        <button type="button"
                            @click="preview=null; filename=''; $refs.fotoInput.value=''"
                            class="absolute top-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-3 py-1">
                            <p class="text-white text-xs truncate" x-text="filename"></p>
                        </div>
                    </div>
                </template>

                <label x-show="!preview"
                    class="flex flex-col items-center justify-center w-full py-8 bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-400 font-medium">Klik untuk upload foto atau ambil gambar</p>
                    <p class="text-xs text-gray-300 dark:text-gray-600 mt-0.5">JPG, PNG, WEBP — Maks 2MB</p>
                    <input type="file" name="foto" accept="image/*" x-ref="fotoInput"
                        class="sr-only"
                        @change="
                            const f=$event.target.files[0];
                            if(f){ filename=f.name; const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(f); }
                        ">
                </label>
                @error('foto')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Info --}}
            <div class="p-4 bg-brand-50 dark:bg-brand-900/20 border border-brand-100 dark:border-brand-800 rounded-xl">
                <p class="text-brand-700 dark:text-brand-300 text-xs leading-relaxed">
                    💡 <strong>Tips:</strong> Sertakan foto dan deskripsi lengkap agar pengaduan lebih cepat diproses.
                    Kami akan mengirimkan notifikasi ketika status pengaduan berubah.
                </p>
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>
@endsection