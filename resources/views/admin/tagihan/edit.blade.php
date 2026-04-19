@extends('layouts.app')

@section('title', 'Edit Tagihan')
@section('page_title', 'Edit Status Tagihan')
@section('page_subtitle', $tagihan->nomor_tagihan)

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.tagihan.show', $tagihan) }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Detail
    </a>
</div>

<div class="max-w-xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <h2 class="font-bold text-gray-800 dark:text-white text-lg mb-1">Update Status Tagihan</h2>
        <p class="text-gray-400 text-sm mb-6">Tagihan: <span class="font-mono font-semibold text-brand-600">{{ $tagihan->nomor_tagihan }}</span> — {{ $tagihan->pelanggan->nama_pelanggan }}</p>

        <form method="POST" action="{{ route('admin.tagihan.update', $tagihan) }}">
            @csrf @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status Tagihan</label>
                <div class="space-y-2.5">
                    @foreach(['belum_bayar' => ['label' => 'Belum Bayar', 'color' => 'amber', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'], 'lunas' => ['label' => 'Lunas', 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'], 'terlambat' => ['label' => 'Terlambat', 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z']] as $val => $opt)
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all
                        {{ $tagihan->status === $val ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <input type="radio" name="status" value="{{ $val }}" {{ $tagihan->status === $val ? 'checked' : '' }}
                            class="text-brand-600 focus:ring-brand-500">
                        <div class="w-8 h-8 rounded-lg bg-{{ $opt['color'] }}-100 dark:bg-{{ $opt['color'] }}-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-{{ $opt['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $opt['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $opt['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.tagihan.show', $tagihan) }}"
                    class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection