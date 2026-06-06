@extends('layouts.app')
@section('title', 'Input Meteran Baru')
@section('page_title', 'Input Meteran Baru')
@section('page_subtitle', 'Catat pemakaian air + foto bukti meteran')

@section('content')

    <div class="mb-4">
        <a href="{{ route('petugas.meteran.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI: FORM --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- STEP 1: Pilih Pelanggan --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3 flex items-center gap-2">
                    <span
                        class="w-6 h-6 bg-brand-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    Pilih Pelanggan
                </h3>
                <form method="GET" action="{{ route('petugas.meteran.create') }}" id="formPilih">
                    @if(request('bulan'))<input type="hidden" name="bulan" value="{{ request('bulan') }}">@endif
                    @if(request('tahun'))<input type="hidden" name="tahun" value="{{ request('tahun') }}">@endif
                    <input type="hidden" name="pelanggan_id" id="hiddenPelangganId" value="{{ request('pelanggan_id') }}">

                    {{-- Search Input --}}
                    <div class="relative">
                        <div
                            class="flex items-center border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus-within:ring-2 focus-within:ring-brand-500 focus-within:border-brand-500 transition-all">
                            <svg class="w-4 h-4 text-gray-400 ml-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="searchPelanggan" placeholder="Ketik nama atau nomor pelanggan..."
                                autocomplete="off"
                                class="flex-1 py-3 px-3 text-sm bg-transparent text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none">
                            <button type="button" id="btnClearSearch" class="hidden mr-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Dropdown hasil pencarian --}}
                        <div id="dropdownPelanggan"
                            class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-64 overflow-y-auto">
                            @foreach($pelangganList as $plg)
                                <div class="pelanggan-option px-4 py-3 cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-900/20 border-b border-gray-50 dark:border-gray-800 last:border-0 transition-colors"
                                    data-id="{{ $plg->id }}" data-nama="{{ $plg->nama_pelanggan }}"
                                    data-nomor="{{ $plg->nomor_pelanggan }}">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($plg->nama_pelanggan, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                                {{ $plg->nama_pelanggan }}</p>
                                            <p class="text-xs text-gray-400">{{ $plg->nomor_pelanggan }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div id="noResult" class="hidden px-4 py-6 text-center text-sm text-gray-400">
                                Pelanggan tidak ditemukan
                            </div>
                        </div>
                    </div>

                    {{-- Pelanggan terpilih --}}
                    <div id="selectedInfo"
                        class="hidden mt-3 flex items-center gap-3 p-3 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-800 rounded-xl">
                        <div id="selectedAvatar"
                            class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-brand-800 dark:text-brand-200" id="selectedNama"></p>
                            <p class="text-xs text-brand-500" id="selectedNomor"></p>
                        </div>
                        <button type="button" id="btnGantiPelanggan"
                            class="text-xs text-brand-600 hover:text-brand-800 font-semibold">Ganti</button>
                    </div>
                </form>
            </div>

            @if($selectedPelanggan)
                {{-- Info Pelanggan --}}
                <div class="bg-brand-50 dark:bg-brand-900/20 rounded-2xl border border-brand-100 dark:border-brand-800 p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-brand-500 dark:text-brand-400 mb-0.5">Nama</p>
                            <p class="font-semibold text-brand-800 dark:text-brand-200">{{ $selectedPelanggan->nama_pelanggan }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 dark:text-brand-400 mb-0.5">No. Pelanggan</p>
                            <p class="font-mono font-semibold text-brand-800 dark:text-brand-200">
                                {{ $selectedPelanggan->nomor_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 dark:text-brand-400 mb-0.5">Alamat</p>
                            <p class="text-brand-700 dark:text-brand-300 text-xs">{{ $selectedPelanggan->alamat }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-brand-500 dark:text-brand-400 mb-0.5">Angka Awal Bulan Ini</p>
                            <p class="font-bold text-brand-800 dark:text-brand-200 text-xl">{{ number_format($angkaAwalRef) }}
                                <span class="text-sm font-normal">m³</span></p>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Form Input + Foto --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5"
                    x-data="{
                        angkaAwal: {{ $angkaAwalRef }},
                        angkaAkhir: {{ old('angka_akhir', 0) }},
                        pemakaian: 0,
                        totalTagihan: 0,
                        biayaPokok: 0,
                        tarifBlok1: {{ $tarifConfig['tarif_blok1'] }},
                        tarifBlok2: {{ $tarifConfig['tarif_blok2'] }},
                        tarifBlok3: {{ $tarifConfig['tarif_blok3'] }},
                        biayaAdmin: {{ $tarifConfig['biaya_admin'] }},
                        fotoPreview: null,
                        fotoNama: '',
                        init(){ this.hitung(); },
                        hitung(){
                         this.pemakaian = Math.max(0, this.angkaAkhir - this.angkaAwal); 
                         let p = this.pemakaian; let t = this.tarifBlok1; 
                         if (p > 10) { t += (Math.min(p, 20) - 10) * this.tarifBlok2; } 
                         if (p > 20) { t += (p - 20) * this.tarifBlok3; } this.biayaPokok = t; 
                         this.totalTagihan = t + this.biayaAdmin; 
                         },
                        handleFoto(e){
                            const f = e.target.files[0];
                            if(!f) return;
                            this.fotoNama = f.name;
                            const r = new FileReader();
                            r.onload = ev => this.fotoPreview = ev.target.result;
                            r.readAsDataURL(f);
                        },
                        hapusFoto(){
                            this.fotoPreview = null;
                            this.fotoNama = '';
                            document.getElementById('inputFoto').value = '';
                        },
                        formatRp(n){ return 'Rp '+Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }
                    }">

                    <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-4 flex items-center gap-2">
                        <span
                            class="w-6 h-6 bg-brand-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Input Data Meteran
                    </h3>

                    <form method="POST" action="{{ route('petugas.meteran.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="pelanggan_id" value="{{ $selectedPelanggan->id }}">

                        {{-- Periode --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Bulan <span
                                        class="text-red-500">*</span></label>
                                <select name="bulan" required
                                    class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                    @foreach(range(1, 12) as $b)
                                        <option value="{{ $b }}" {{ old('bulan', now()->month) == $b ? 'selected' : '' }}>
                                            {{ \App\Services\TagihanService::namaBulan($b) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bulan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tahun <span
                                        class="text-red-500">*</span></label>
                                <select name="tahun" required
                                    class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                    @foreach(range(now()->year, now()->year - 2) as $y)
                                        <option value="{{ $y }}" {{ old('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Angka Meteran --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Angka
                                    Awal</label>
                                <div class="relative">
                                    <input type="number" readonly :value="angkaAwal"
                                        class="w-full py-2.5 px-4 pr-10 text-sm bg-gray-100 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">m³</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Dari data bulan sebelumnya</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Angka Akhir
                                    <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="angka_akhir" x-model.number="angkaAkhir" @input="hitung()"
                                        value="{{ old('angka_akhir') }}" step="0.01" required placeholder="Baca dari meteran..."
                                        class="w-full py-2.5 px-4 pr-10 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">m³</span>
                                </div>
                                @error('angka_akhir')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Pemakaian Preview --}}
                        <div class="mb-4 p-3 rounded-xl border-2 flex items-center justify-between transition-all"
                            :class="pemakaian > 0 ? 'bg-teal-50 dark:bg-teal-900/20 border-teal-200 dark:border-teal-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'">
                            <span class="text-sm font-semibold"
                                :class="pemakaian > 0 ? 'text-teal-700 dark:text-teal-300' : 'text-gray-400'">Pemakaian
                                Air</span>
                            <span class="text-2xl font-extrabold"
                                :class="pemakaian > 0 ? 'text-teal-700 dark:text-teal-300' : 'text-gray-300 dark:text-gray-600'"
                                x-text="pemakaian.toFixed(1) + ' m³'">0 m³</span>
                        </div>

                        {{-- Tanggal Baca --}}
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Baca
                                <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_baca" value="{{ old('tanggal_baca', now()->toDateString()) }}"
                                max="{{ now()->toDateString() }}" required
                                class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            @error('tanggal_baca')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- FOTO METER — kamera atau galeri --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Foto Meteran
                                <span class="text-gray-400 font-normal text-xs">(opsional, maks 5MB)</span>
                            </label>

                            {{-- Preview foto --}}
                            <template x-if="fotoPreview">
                                <div
                                    class="relative mb-3 rounded-xl overflow-hidden border-2 border-brand-300 dark:border-brand-700">
                                    <img :src="fotoPreview" class="w-full max-h-48 object-cover">
                                    <div class="absolute top-2 right-2 flex gap-1.5">
                                        <button type="button" @click="hapusFoto()"
                                            class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 px-3 py-2">
                                        <p class="text-white text-xs truncate" x-text="fotoNama"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- Upload buttons --}}
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Tombol Kamera (mobile) --}}
                                <label
                                    class="flex flex-col items-center gap-2 p-4 bg-brand-50 dark:bg-brand-900/20 border-2 border-dashed border-brand-200 dark:border-brand-700 rounded-xl cursor-pointer hover:bg-brand-100 dark:hover:bg-brand-900/30 transition-all">
                                    <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-brand-700 dark:text-brand-300">Buka Kamera</span>
                                    <span class="text-xs text-brand-500 dark:text-brand-400">Foto langsung</span>
                                    <input type="file" name="foto_meter" id="inputFoto" accept="image/*" capture="environment"
                                        class="sr-only" @change="handleFoto($event)">
                                </label>

                                {{-- Tombol Galeri --}}
                                <label
                                    class="flex flex-col items-center gap-2 p-4 bg-gray-50 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Pilih dari
                                        Galeri</span>
                                    <span class="text-xs text-gray-400">JPG, PNG, WEBP</span>
                                    <input type="file" name="foto_meter" id="inputFotoGaleri" accept="image/*" class="sr-only"
                                        @change="handleFoto($event); document.getElementById('inputFoto').value=''">
                                </label>
                            </div>
                            @error('foto_meter')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

                            <p class="text-xs text-gray-400 mt-2">
                                💡 Foto meteran digunakan sebagai bukti pembacaan dan antisipasi sengketa.
                                Disarankan foto jelas menampilkan angka meteran.
                            </p>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Keterangan
                                (Opsional)</label>
                            <textarea name="keterangan" rows="2" placeholder="Kondisi meteran, catatan khusus, dll..."
                                class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('keterangan') }}</textarea>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Simpan & Generate Tagihan
                        </button>
                    </form>
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-16 text-center">
                    <svg class="w-14 h-14 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="font-semibold text-gray-400 dark:text-gray-500">Pilih pelanggan terlebih dahulu</p>
                    <p class="text-sm text-gray-300 dark:text-gray-600 mt-1">Form input meteran akan muncul di sini</p>
                </div>
            @endif
        </div>

        {{-- KANAN: Preview + Tarif --}}
        <div class="space-y-4">
            @if($selectedPelanggan)
                {{-- Live Preview Tagihan --}}
                <div class="bg-gradient-to-br from-brand-800 to-brand-600 rounded-2xl p-5 text-white sticky top-20" x-data="{
                    angkaAwal: {{ $angkaAwalRef }}, angkaAkhir: 0, 
                    tarifBlok1: {{ $tarifConfig['tarif_blok1'] }}, 
                    tarifBlok2: {{ $tarifConfig['tarif_blok2'] }}, 
                    tarifBlok3: {{ $tarifConfig['tarif_blok3'] }}, 
                    biayaAdmin: {{ $tarifConfig['biaya_admin'] }}, 
                    get pemakaian() { return Math.max(0, this.angkaAkhir - this.angkaAwal); }, 
                    get biayaPokok() { 
                    let p = this.pemakaian; 
                    let t = this.tarifBlok1; 
                    if (p > 10) { 
                        t += (Math.min(p,20)-10) * this.tarifBlok2; 
                    } 
                    if (p > 20) { 
                        t += (p-20) * this.tarifBlok3; 
                    } 
                        return t; 
                    }, 
                    get totalTagihan() { 
                        return this.biayaPokok + this.biayaAdmin; 
                    }, 
                    formatRp(n){
                      return 'Rp ' + Math.round(n) .toString() .replace(/\B(?=(\d{3})+(?!\d))/g,'.'); 
                    } 
                      }" @input.window="if($event.target.name==='angka_akhir') angkaAkhir=parseFloat($event.target.value)||0">

                    <p class="text-brand-200 text-xs font-medium uppercase tracking-wider mb-1">Estimasi Tagihan</p>
                    <p class="text-4xl font-extrabold mb-5" x-text="formatRp(totalTagihan)">Rp 0</p>

                    <div class="space-y-2 text-sm border-t border-brand-600 pt-4">
                        <div class="flex justify-between"><span class="text-brand-300">Angka Awal</span><span
                                class="font-semibold">{{ number_format($angkaAwalRef) }} m³</span></div>
                        <div class="flex justify-between"><span class="text-brand-300">Angka Akhir</span><span
                                class="font-semibold" x-text="angkaAkhir + ' m³'">0 m³</span></div>
                        <div class="flex justify-between border-t border-brand-600 pt-2">
                            <span class="text-teal-300 font-semibold">Pemakaian</span>
                            <span class="font-bold text-teal-300" x-text="pemakaian.toFixed(1) + ' m³'">0 m³</span>
                        </div>
                        <div class="flex justify-between"><span class="text-brand-300">Biaya Air</span><span
                                class="font-semibold" x-text="formatRp(biayaPokok)">Rp 0</span></div>
                        <div class="flex justify-between"><span class="text-brand-300">Biaya Admin</span><span
                                class="font-semibold" x-text="formatRp(biayaAdmin)"></span></div>
                        <div class="flex justify-between border-t border-brand-500 pt-2">
                            <span class="text-white font-bold">TOTAL</span>
                            <span class="font-extrabold text-lg" x-text="formatRp(totalTagihan)">Rp 0</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Info Tarif --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">📋 Tarif Air</h3>
                <div class="space-y-2">

                    <div
                        class="flex items-center justify-between py-2 px-3 rounded-xl bg-emerald-50 border border-emerald-100">
                        <div>
                            <p class="text-xs font-bold text-emerald-700">Blok 1</p>
                            <p class="text-xs text-emerald-500">0 – 10 m³</p>
                        </div>
                        <p class="text-sm font-bold text-emerald-700">
                            Rp {{ number_format($tarifConfig['tarif_blok1'], 0, ',', '.') }} flat
                        </p>
                    </div>

                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-blue-50 border border-blue-100">
                        <div>
                            <p class="text-xs font-bold text-blue-700">Blok 2</p>
                            <p class="text-xs text-blue-500">11 – 20 m³</p>
                        </div>
                        <p class="text-sm font-bold text-blue-700">
                            Rp {{ number_format($tarifConfig['tarif_blok2'], 0, ',', '.') }}/m³
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-between py-2 px-3 rounded-xl bg-orange-50 border border-orange-100">
                        <div>
                            <p class="text-xs font-bold text-orange-700">Blok 3</p>
                            <p class="text-xs text-orange-500">&gt; 20 m³</p>
                        </div>
                        <p class="text-sm font-bold text-orange-700">
                            Rp {{ number_format($tarifConfig['tarif_blok3'], 0, ',', '.') }}/m³
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-between py-2 px-3 rounded-xl bg-purple-50 border border-purple-100">
                        <div>
                            <p class="text-xs font-bold text-purple-700">Administrasi</p>
                            <p class="text-xs text-purple-500">Semua pemakaian</p>
                        </div>
                        <p class="text-sm font-bold text-purple-700">
                            Rp {{ number_format($tarifConfig['biaya_admin'], 0, ',', '.') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchPelanggan');
            const dropdown = document.getElementById('dropdownPelanggan');
            const hiddenId = document.getElementById('hiddenPelangganId');
            const selectedInfo = document.getElementById('selectedInfo');
            const selectedNama = document.getElementById('selectedNama');
            const selectedNomor = document.getElementById('selectedNomor');
            const selectedAvatar = document.getElementById('selectedAvatar');
            const btnClear = document.getElementById('btnClearSearch');
            const btnGanti = document.getElementById('btnGantiPelanggan');
            const options = document.querySelectorAll('.pelanggan-option');
            const noResult = document.getElementById('noResult');

            // Jika sudah ada pelanggan terpilih dari URL
            const currentId = hiddenId.value;
            if (currentId) {
                const opt = document.querySelector(`.pelanggan-option[data-id="${currentId}"]`);
                if (opt) tampilkanTerpilih(opt.dataset.id, opt.dataset.nama, opt.dataset.nomor);
            }

            // Tampilkan dropdown saat fokus
            searchInput.addEventListener('focus', function () {
                filterOptions(this.value);
                dropdown.classList.remove('hidden');
            });

            // Filter saat mengetik
            searchInput.addEventListener('input', function () {
                filterOptions(this.value);
                dropdown.classList.remove('hidden');
                btnClear.classList.toggle('hidden', !this.value);
            });

            // Klik opsi
            options.forEach(opt => {
                opt.addEventListener('click', function () {
                    pilihPelanggan(this.dataset.id, this.dataset.nama, this.dataset.nomor);
                });
            });

            // Tombol clear
            btnClear.addEventListener('click', function () {
                searchInput.value = '';
                filterOptions('');
                btnClear.classList.add('hidden');
                searchInput.focus();
            });

            // Tombol ganti pelanggan
            btnGanti.addEventListener('click', function () {
                selectedInfo.classList.add('hidden');
                searchInput.value = '';
                hiddenId.value = '';
                filterOptions('');
                dropdown.classList.remove('hidden');
                searchInput.focus();
            });

            // Tutup dropdown kalau klik di luar
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#searchPelanggan') &&
                    !e.target.closest('#dropdownPelanggan') &&
                    !e.target.closest('#btnClearSearch')) {
                    dropdown.classList.add('hidden');
                }
            });

            function filterOptions(q) {
                q = q.toLowerCase().trim();
                let ada = false;
                options.forEach(opt => {
                    const nama = opt.dataset.nama.toLowerCase();
                    const nomor = opt.dataset.nomor.toLowerCase();
                    const cocok = !q || nama.includes(q) || nomor.includes(q);
                    opt.style.display = cocok ? '' : 'none';
                    if (cocok) ada = true;
                });
                noResult.classList.toggle('hidden', ada);
            }

            function tampilkanTerpilih(id, nama, nomor) {
                hiddenId.value = id;
                selectedNama.textContent = nama;
                selectedNomor.textContent = nomor;
                selectedAvatar.textContent = nama.substring(0, 2).toUpperCase();
                selectedInfo.classList.remove('hidden');
                searchInput.value = '';
                dropdown.classList.add('hidden');
                btnClear.classList.add('hidden');
            }

            function pilihPelanggan(id, nama, nomor) {
                tampilkanTerpilih(id, nama, nomor);
                // Submit form untuk load data pelanggan
                setTimeout(() => document.getElementById('formPilih').submit(), 150);
            }

            // Lock bulan masa depan
            const bulanSel = document.querySelector('select[name="bulan"]');
            const tahunSel = document.querySelector('select[name="tahun"]');
            if (bulanSel && tahunSel) {
                function lockFutureBulan() {
                    const bulanNow = new Date().getMonth() + 1;
                    const tahunNow = new Date().getFullYear();
                    const tahun = parseInt(tahunSel.value);
                    Array.from(bulanSel.options).forEach(opt => {
                        const b = parseInt(opt.value);
                        opt.disabled = (tahun > tahunNow) || (tahun === tahunNow && b > bulanNow);
                    });
                    if (bulanSel.options[bulanSel.selectedIndex]?.disabled) {
                        bulanSel.value = tahun === tahunNow ? bulanNow : 1;
                    }
                }
                tahunSel.addEventListener('change', lockFutureBulan);
                lockFutureBulan();
            }
        });
    </script>
@endpush