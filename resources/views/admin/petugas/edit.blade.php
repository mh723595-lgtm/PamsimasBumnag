@extends('layouts.app')
@section('title','Edit Petugas')
@section('page_title','Edit Petugas')
@section('page_subtitle', $petugas->nama_petugas)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.petugas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">

        {{-- Info TMT --}}
        @if($petugas->tmt)
        <div class="mb-5 p-4 bg-brand-50 dark:bg-brand-900/20 rounded-xl border border-brand-100 dark:border-brand-800 flex items-center gap-3">
            <svg class="w-8 h-8 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <div>
                <p class="text-sm font-semibold text-brand-700 dark:text-brand-300">Bergabung sejak {{ $petugas->tmt->format('d F Y') }}</p>
                <p class="text-xs text-brand-500">Masa kerja: <strong>{{ $petugas->lamaBergabung() }}</strong></p>
            </div>
        </div>
        @endif

        {{-- Foto Preview --}}
        <div class="flex flex-col items-center mb-6">
            <div class="relative">
                <img id="foto-preview" src="{{ $petugas->fotoUrl() }}"
                    class="w-24 h-24 rounded-2xl object-cover border-4 border-brand-100 shadow">
                <label for="foto" class="absolute -bottom-2 -right-2 w-8 h-8 bg-brand-500 hover:bg-brand-600 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </label>
            </div>
            <p class="text-xs text-gray-400 mt-3">Klik ikon kamera untuk ganti foto</p>
        </div>

        <form method="POST" action="{{ route('admin.petugas.update', $petugas) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <input type="file" id="foto" name="foto" accept="image/*" class="hidden">

            <div class="grid grid-cols-2 gap-4">

                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Petugas *</label>
                    <input type="text" name="nama_petugas" value="{{ old('nama_petugas', $petugas->nama_petugas) }}" required
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik', $petugas->nik) }}" maxlength="16"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('nik')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $petugas->no_hp) }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jabatan</label>
                    <select name="jabatan"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatanList as $j)
                        <option value="{{ $j }}" {{ old('jabatan', $petugas->jabatan) == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select name="status"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <option value="aktif"    {{ old('status', $petugas->status) == 'aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $petugas->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $petugas->tanggal_lahir?->format('Y-m-d')) }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">TMT (Terhitung Mulai Tanggal)</label>
                    <input type="date" name="tmt" value="{{ old('tmt', $petugas->tmt?->format('Y-m-d')) }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @if($petugas->tmt)
                    <p class="text-xs text-brand-500 mt-1">Masa kerja: {{ $petugas->lamaBergabung() }}</p>
                    @endif
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('alamat', $petugas->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">Simpan Perubahan</button>
                <a href="{{ route('admin.petugas.index') }}" class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('foto-preview').src = e.target.result;
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection