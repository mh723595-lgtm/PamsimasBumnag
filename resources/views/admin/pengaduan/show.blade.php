@extends('layouts.app')

@section('title', 'Detail Pengaduan')
@section('page_title', 'Detail Pengaduan')
@section('page_subtitle', $pengaduan->nomor_pengaduan)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pengaduan.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Isi Pengaduan --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-1">{{ $pengaduan->nomor_pengaduan }}</p>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $pengaduan->judul }}</h2>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pengaduan->statusBadge() }}">
                            {{ ucfirst($pengaduan->status) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                            {{ $pengaduan->prioritas === 'tinggi' ? '🔴' : ($pengaduan->prioritas === 'sedang' ? '🟡' : '🟢') }} {{ ucfirst($pengaduan->prioritas) }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 capitalize">
                            {{ $pengaduan->jenis }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Deskripsi Pengaduan</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $pengaduan->deskripsi }}</p>
            </div>

            {{-- Foto --}}
            @if($pengaduan->foto)
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Foto Pengaduan</h3>
                <img src="{{ Storage::url($pengaduan->foto) }}" alt="Foto pengaduan"
                    class="max-h-64 rounded-xl object-cover border border-gray-100 dark:border-gray-800 shadow-sm">
            </div>
            @endif

            {{-- Info Pelanggan --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Pelapor</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($pengaduan->pelanggan->nama_pelanggan, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pengaduan->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $pengaduan->pelanggan->nomor_pelanggan }} · {{ $pengaduan->pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>

            {{-- Tanggapan yang sudah ada --}}
            @if($pengaduan->tanggapan)
            <div class="p-5 bg-emerald-50 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900">
                <h3 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">Tanggapan Admin</h3>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $pengaduan->tanggapan }}</p>
                @if($pengaduan->ditanganiOleh)
                <p class="text-xs text-gray-400 mt-2">oleh: {{ $pengaduan->ditanganiOleh->name }}
                    @if($pengaduan->tanggal_selesai) · {{ $pengaduan->tanggal_selesai->format('d/m/Y H:i') }} @endif
                </p>
                @endif
            </div>
            @endif

            {{-- Timeline --}}
            <div class="p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Timeline</h3>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span>Dibuat: {{ $pengaduan->created_at->format('d/m/Y H:i') }}</span>
                    @if($pengaduan->tanggal_selesai)
                    <span>·</span>
                    <span>Selesai: {{ $pengaduan->tanggal_selesai->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Form Tanggapi --}}
    <div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sticky top-20">
            <h3 class="font-bold text-gray-800 dark:text-white mb-4">Tangani Pengaduan</h3>

            <form method="POST" action="{{ route('admin.pengaduan.update', $pengaduan) }}">
                @csrf @method('PUT')

                {{-- Status --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Status</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['baru'=>['Baru','blue'],'diproses'=>['Diproses','amber'],'selesai'=>['Selesai','emerald'],'ditolak'=>['Ditolak','red']] as $val=>[$label,$color])
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 cursor-pointer text-xs font-semibold transition-all
                            {{ $pengaduan->status === $val ? "border-{$color}-500 bg-{$color}-50 dark:bg-{$color}-900/20 text-{$color}-700 dark:text-{$color}-300" : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300' }}">
                            <input type="radio" name="status" value="{{ $val }}" {{ $pengaduan->status === $val ? 'checked' : '' }} class="sr-only">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Prioritas --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Prioritas</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['tinggi'=>'🔴 Tinggi','sedang'=>'🟡 Sedang','rendah'=>'🟢 Rendah'] as $val=>$label)
                        <label class="flex items-center justify-center p-2.5 rounded-xl border-2 cursor-pointer text-xs font-semibold transition-all
                            {{ $pengaduan->prioritas === $val ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20 text-brand-700 dark:text-brand-300' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300' }}">
                            <input type="radio" name="prioritas" value="{{ $val }}" {{ $pengaduan->prioritas === $val ? 'checked' : '' }} class="sr-only">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Tanggapan --}}
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">
                        Tanggapan <span class="text-gray-400 font-normal">(wajib jika selesai/ditolak)</span>
                    </label>
                    <textarea name="tanggapan" rows="4"
                        placeholder="Tulis tanggapan atau keterangan penanganan..."
                        class="w-full py-3 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('tanggapan', $pengaduan->tanggapan) }}</textarea>
                    @error('tanggapan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan & Kirim Notifikasi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection