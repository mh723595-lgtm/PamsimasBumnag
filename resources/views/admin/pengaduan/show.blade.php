@extends('layouts.app')
@section('title','Detail Pengaduan')
@section('page_title','Detail Pengaduan')
@section('page_subtitle', $pengaduan->nomor_pengaduan)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pengaduan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Pengaduan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Isi Pengaduan --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-1">{{ $pengaduan->nomor_pengaduan }}</p>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-2">{{ $pengaduan->judul }}</h2>
                <div class="flex flex-wrap gap-2">
                    @php
                    $statusCls = match($pengaduan->status) {
                        'baru'     => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                        'diproses' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                        'selesai'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                        default    => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    };
                    $prioritasIco = match($pengaduan->prioritas) { 'tinggi'=>'🔴','sedang'=>'🟡',default=>'🟢' };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusCls }}">
                        {{ ucfirst($pengaduan->status) }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        {{ $prioritasIco }} {{ ucfirst($pengaduan->prioritas) }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">
                        {{ $pengaduan->jenis }}
                    </span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $pengaduan->deskripsi }}</p>
            </div>

            {{-- Foto --}}
            @if($pengaduan->foto)
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Foto Bukti</h3>
                <img src="{{ \Storage::url($pengaduan->foto) }}" alt="Foto pengaduan"
                    class="max-h-64 rounded-xl object-cover border border-gray-100 dark:border-gray-800 cursor-pointer"
                    onclick="this.classList.toggle('max-h-64');this.classList.toggle('max-h-full')">
                <p class="text-xs text-gray-400 mt-1">Klik foto untuk perbesar</p>
            </div>
            @endif

            {{-- Pelapor --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pelapor</h3>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($pengaduan->pelanggan->nama_pelanggan, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pengaduan->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $pengaduan->pelanggan->nomor_pelanggan }}</p>
                    </div>
                </div>
            </div>

            {{-- Tanggapan lama --}}
            @if($pengaduan->tanggapan)
            <div class="p-5 bg-emerald-50 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900">
                <h3 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">Tanggapan Sebelumnya</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $pengaduan->tanggapan }}</p>
                @if($pengaduan->ditanganiOleh)
                <p class="text-xs text-gray-400 mt-1">oleh: {{ $pengaduan->ditanganiOleh->name }}
                    @if($pengaduan->tanggal_selesai)· {{ $pengaduan->tanggal_selesai->format('d/m/Y H:i') }}@endif
                </p>
                @endif
            </div>
            @endif

            <div class="p-4">
                <p class="text-xs text-gray-400">📅 Dikirim: {{ $pengaduan->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Form Tangani --}}
    <div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 dark:text-white mb-4 text-base">🛠️ Tangani Pengaduan</h3>

            <form method="POST" action="{{ route('admin.pengaduan.tanggapi', $pengaduan) }}">
                @csrf

                {{-- Status — SELECT NATIVE (bisa dipilih di HP) --}}
                <div class="mb-4">
                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:border-brand-500 focus:ring-0 transition-all appearance-none">
                        <option value="baru"     {{ $pengaduan->status==='baru'     ? 'selected':'' }}>📥 Baru</option>
                        <option value="diproses" {{ $pengaduan->status==='diproses' ? 'selected':'' }}>⚙️ Sedang Diproses</option>
                        <option value="selesai"  {{ $pengaduan->status==='selesai'  ? 'selected':'' }}>✅ Selesai</option>
                        <option value="ditolak"  {{ $pengaduan->status==='ditolak'  ? 'selected':'' }}>❌ Ditolak</option>
                    </select>
                    @error('status')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Prioritas — SELECT NATIVE (bisa dipilih di HP) --}}
                <div class="mb-4">
                    <label for="prioritas" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select id="prioritas" name="prioritas" required
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:border-brand-500 focus:ring-0 transition-all appearance-none">
                        <option value="tinggi"  {{ $pengaduan->prioritas==='tinggi'  ? 'selected':'' }}>🔴 Tinggi — Segera ditangani</option>
                        <option value="sedang"  {{ $pengaduan->prioritas==='sedang'  ? 'selected':'' }}>🟡 Sedang — Normal</option>
                        <option value="rendah"  {{ $pengaduan->prioritas==='rendah'  ? 'selected':'' }}>🟢 Rendah — Tidak mendesak</option>
                    </select>
                    @error('prioritas')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggapan --}}
                <div class="mb-5">
                    <label for="tanggapan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Tanggapan
                        <span class="text-xs font-normal text-gray-400">(wajib jika selesai/ditolak)</span>
                    </label>
                    <textarea id="tanggapan" name="tanggapan" rows="5"
                        placeholder="Tulis tanggapan atau keterangan penanganan..."
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-0 resize-none transition-all">{{ old('tanggapan', $pengaduan->tanggapan) }}</textarea>
                    @error('tanggapan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan & Kirim Notifikasi
                </button>

                <p class="text-xs text-gray-400 text-center mt-3">
                    Notifikasi akan dikirim otomatis ke pelanggan
                </p>
            </form>
        </div>
    </div>
</div>
@endsection