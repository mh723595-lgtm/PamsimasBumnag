{{-- resources/views/admin/pelanggan/edit.blade.php --}}
@extends('layouts.app')
@section('title','Edit Pelanggan')
@section('page_title','Edit Pelanggan')
@section('page_subtitle',$pelanggan->nomor_pelanggan)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pelanggan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.pelanggan.update', $pelanggan) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Pelanggan *</label>
                    <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Alamat *</label>
                    <textarea name="alamat" rows="2" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                </div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">RT/RW</label><input type="text" name="rt_rw" value="{{ old('rt_rw', $pelanggan->rt_rw) }}" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Desa</label><input type="text" name="desa" value="{{ old('desa', $pelanggan->desa) }}" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label><input type="text" name="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Meteran Awal *</label><input type="number" name="meteran_awal" value="{{ old('meteran_awal', $pelanggan->meteran_awal) }}" min="0" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></div>
                <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Daftar</label><input type="date" name="tanggal_daftar" value="{{ old('tanggal_daftar', $pelanggan->tanggal_daftar->format('Y-m-d')) }}" required class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"></div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select name="status" class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        @foreach(['aktif','nonaktif','tutup'] as $s)
                        <option value="{{ $s }}" {{ $pelanggan->status===$s ? 'selected':'' }} class="capitalize">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">Simpan</button>
                <a href="{{ route('admin.pelanggan.index') }}" class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection