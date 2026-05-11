@extends('layouts.app')
@section('title','Edit Tagihan')
@section('page_title','Update Status Tagihan')
@section('page_subtitle', $tagihan->nomor_tagihan . ' — ' . $tagihan->pelanggan->nama_pelanggan)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tagihan.show', $tagihan) }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Detail Tagihan
    </a>
</div>

<div class="max-w-xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6"
        x-data="{ status: '{{ $tagihan->status }}' }">

        <h2 class="font-bold text-gray-800 dark:text-white text-lg mb-1">Update Status Tagihan</h2>
        <p class="text-gray-400 text-sm mb-6">
            Tagihan: <span class="font-mono font-semibold text-brand-600">{{ $tagihan->nomor_tagihan }}</span>
            — {{ $tagihan->pelanggan->nama_pelanggan }}
        </p>

        <form method="POST" action="{{ route('admin.tagihan.update', $tagihan) }}">
            @csrf @method('PUT')

            {{-- FIX: Status pakai label interaktif dengan static CSS classes (tidak dynamic Tailwind) --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Status Tagihan</label>
                <div class="space-y-2.5">

                    {{-- Belum Bayar --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                        :class="status === 'belum_bayar'
                            ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20'
                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                        <input type="radio" name="status" value="belum_bayar"
                            x-model="status"
                            {{ $tagihan->status === 'belum_bayar' ? 'checked' : '' }}
                            class="text-amber-500 focus:ring-amber-400 w-4 h-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Belum Bayar</p>
                            <p class="text-xs text-gray-400">Tagihan belum dilunasi</p>
                        </div>
                    </label>

                    {{-- Lunas --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                        :class="status === 'lunas'
                            ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20'
                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                        <input type="radio" name="status" value="lunas"
                            x-model="status"
                            {{ $tagihan->status === 'lunas' ? 'checked' : '' }}
                            class="text-emerald-500 focus:ring-emerald-400 w-4 h-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Lunas</p>
                            <p class="text-xs text-gray-400">Otomatis buat record pembayaran</p>
                        </div>
                    </label>

                    {{-- Terlambat --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                        :class="status === 'terlambat'
                            ? 'border-red-400 bg-red-50 dark:bg-red-900/20'
                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                        <input type="radio" name="status" value="terlambat"
                            x-model="status"
                            {{ $tagihan->status === 'terlambat' ? 'checked' : '' }}
                            class="text-red-500 focus:ring-red-400 w-4 h-4">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">Terlambat</p>
                            <p class="text-xs text-gray-400">Melewati jatuh tempo</p>
                        </div>
                    </label>
                </div>
                @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- FIX: Opsi pembayaran muncul jika pilih Lunas --}}
            <div x-show="status === 'lunas'" x-transition class="mb-5 space-y-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">Detail Pembayaran (opsional)</p>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Metode Bayar</label>
                        <select name="metode_bayar"
                            class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <option value="tunai">💵 Tunai</option>
                            <option value="transfer">🏦 Transfer</option>
                            <option value="lainnya">📋 Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar"
                            value="{{ now()->toDateString() }}"
                            class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Catatan</label>
                    <input type="text" name="catatan"
                        placeholder="Catatan pembayaran..."
                        value="Dikonfirmasi manual oleh admin"
                        class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>
                <p class="text-xs text-emerald-600 dark:text-emerald-400">
                    ✅ Record pembayaran akan otomatis dibuat dan pendapatan akan tercatat.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
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