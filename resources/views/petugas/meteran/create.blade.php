@extends('layouts.app')

@section('title', 'Input Meteran Baru')
@section('page_title', 'Input Meteran Baru')
@section('page_subtitle', 'Catat pemakaian air pelanggan bulan ini')
{{-- memperbaiki fitur input meteran --}}
Memperbaiki error blade dan menambahkan fitur input meteran

@section('content')

<div class="mb-4">
    <a href="{{ route('petugas.meteran.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5"
    x-data="{
        angkaAwal: {{ $selectedPelanggan ? $selectedPelanggan->meteran_awal : 0 }},
        angkaAkhir: 0,
        pemakaian: 0,
        totalTagihan: 0,
        biayaPokok: 0,
        hitungTagihan() {
            this.pemakaian = Math.max(0, this.angkaAkhir - this.angkaAwal);
            let p = this.pemakaian;
            let total = 0;
            if (p <= 10) {
                total = 20000;
            } else {
                total = 20000;
                let blok2 = Math.min(p, 20) - 10;
                total += blok2 * 1500;
                if (p > 20) {
                    let blok3 = p - 20;
                    total += blok3 * 2000;
                }
            }
            this.biayaPokok = total;
            total += 0;
            this.totalTagihan = total;
        },
        formatRp(n) {
            return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }">

    {{-- FORM --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
            <form method="POST" action="{{ route('petugas.meteran.store') }}">
                @csrf
                

                {{-- Pilih Pelanggan --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select name="pelanggan_id" required
                        onchange="this.form.submit()"
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelangganList as $plg)
                        <option value="{{ $plg->id }}"
                            {{ (old('pelanggan_id', $selectedPelanggan?->id) == $plg->id) ? 'selected' : '' }}>
                            {{ $plg->nomor_pelanggan }} — {{ $plg->nama_pelanggan }}
                        </option>
                        @endforeach
                    </select>
                    @error('pelanggan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                @if($selectedPelanggan)
                {{-- Info Pelanggan --}}
                <div class="mb-5 p-4 bg-brand-50 dark:bg-brand-900/20 rounded-xl border border-brand-100 dark:border-brand-800">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-brand-500 mb-0.5">Nama Pelanggan</p>
                            <p class="font-semibold text-brand-800 dark:text-brand-200">{{ $selectedPelanggan->nama_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 mb-0.5">Nomor Pelanggan</p>
                            <p class="font-semibold text-brand-800 dark:text-brand-200">{{ $selectedPelanggan->nomor_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 mb-0.5">Alamat</p>
                            <p class="text-brand-700 dark:text-brand-300">{{ $selectedPelanggan->alamat }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 mb-0.5">Meteran Awal (Referensi)</p>
                            <p class="font-bold text-brand-800 dark:text-brand-200 text-lg">{{ number_format($selectedPelanggan->meteran_awal) }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    {{-- Bulan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Bulan <span class="text-red-500">*</span></label>
                        <select name="bulan" required
                            class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            @foreach(range(1, 12) as $b)
                            <option value="{{ $b }}" {{ old('bulan', now()->month) == $b ? 'selected' : '' }}>
                                {{ \App\Services\TagihanService::namaBulan($b) }}
                            </option>
                            @endforeach
                        </select>
                        @error('bulan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tahun --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tahun <span class="text-red-500">*</span></label>
                        <select name="tahun" required
                            class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            @foreach(range(now()->year, now()->year - 2) as $y)
                            <option value="{{ $y }}" {{ old('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    {{-- Angka Awal (readonly) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Angka Awal Meteran</label>
                        <div class="relative">
                            <input type="number" readonly
                                :value="angkaAwal"
                                class="w-full py-3 px-4 text-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">m³</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Otomatis dari data sebelumnya</p>
                    </div>

                    {{-- Angka Akhir --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Angka Akhir Meteran <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="angka_akhir"
                                x-model.number="angkaAkhir"
                                @input="hitungTagihan()"
                                value="{{ old('angka_akhir') }}"
                                min="{{ $selectedPelanggan->meteran_awal }}"
                                step="0.01" required
                                placeholder="0"
                                class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">m³</span>
                        </div>
                        @error('angka_akhir') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Pemakaian Preview --}}
                <div class="mb-5 p-3 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-800 flex items-center justify-between">
                    <p class="text-sm font-semibold text-teal-700 dark:text-teal-300">Pemakaian Air (m³)</p>
                    <p class="text-2xl font-extrabold text-teal-700 dark:text-teal-300" x-text="pemakaian.toFixed(1) + ' m³'">0 m³</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    {{-- Tanggal Baca --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Baca <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_baca"
                            value="{{ old('tanggal_baca', now()->toDateString()) }}"
                            max="{{ now()->toDateString() }}" required
                            class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        @error('tanggal_baca') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="2"
                        placeholder="Catatan tambahan jika ada..."
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('keterangan') }}</textarea>
                </div>

                @else
                <div class="flex items-center justify-center py-16 text-gray-400">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="font-medium">Pilih pelanggan terlebih dahulu</p>
                    </div>
                </div>
                @endif

                @if($selectedPelanggan)
                <button type="submit"
                    class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Simpan & Generate Tagihan
                </button>
                @endif
            </form>
        </div>
    </div>

    {{-- PREVIEW TAGIHAN --}}
    <div class="space-y-4">
        {{-- Live Preview --}}
        <div class="bg-linear-to-br from-brand-800 to-brand-600 rounded-2xl p-5 text-white sticky top-20">
            <p class="text-brand-200 text-xs font-medium uppercase tracking-wider mb-1">Estimasi Tagihan</p>
            <p class="text-4xl font-extrabold mb-6" x-text="formatRp(totalTagihan)">Rp 0</p>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-brand-600">
                    <span class="text-brand-300">Angka Awal</span>
                    <span class="font-semibold" x-text="angkaAwal + ' m³'">0 m³</span>
                </div>
                <div class="flex justify-between py-2 border-b border-brand-600">
                    <span class="text-brand-300">Angka Akhir</span>
                    <span class="font-semibold" x-text="angkaAkhir + ' m³'">0 m³</span>
                </div>
                <div class="flex justify-between py-2 border-b border-brand-600">
                    <span class="text-brand-300">Pemakaian</span>
                    <span class="font-bold text-teal-300" x-text="pemakaian.toFixed(1) + ' m³'">0 m³</span>
                </div>
                <div class="flex justify-between py-2 border-b border-brand-600">
                    <span class="text-brand-300">Biaya Air</span>
                    <span class="font-semibold" x-text="formatRp(biayaPokok)">Rp 0</span>
                </div>
                <div class="flex justify-between py-2 border-b border-brand-600">
                    <span class="text-brand-300">Biaya Admin</span>
                    <span class="font-semibold">Rp 0</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-white font-bold">TOTAL</span>
                    <span class="font-extrabold text-lg" x-text="formatRp(totalTagihan)">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- Info Tarif --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">Info Tarif</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-800">
                    <span class="text-gray-500">0 – 10 m³</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rp 20.000 (flat)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-800">
                    <span class="text-gray-500">11 – 20 m³</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rp 1.500 / m³</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-800">
                    <span class="text-gray-500">> 20 m³</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rp 2.000 / m³</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-500">Biaya Admin</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection