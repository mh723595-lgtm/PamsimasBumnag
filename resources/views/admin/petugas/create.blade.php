{{-- resources/views/admin/petugas/create.blade.php --}}
@extends('layouts.app')
@section('title','Tambah Petugas')
@section('page_title','Tambah Petugas Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.petugas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>
<div class="max-w-xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.petugas.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Petugas *</label>
                    <input type="text" name="nama_petugas" value="{{ old('nama_petugas') }}" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('nama_petugas')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password *</label>
                    <input type="password" name="password" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Teknisi Lapangan" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('alamat') }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">Tambah Petugas</button>
                <a href="{{ route('admin.petugas.index') }}" class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- ============================================ --}}
{{-- LAPORAN PEMAKAIAN VIEW --}}
{{-- resources/views/admin/laporan/pemakaian.blade.php --}}