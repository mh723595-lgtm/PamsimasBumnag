{{-- resources/views/petugas/pengaduan/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pengaduan')
@section('page_title','Detail Pengaduan')
@section('page_subtitle',$pengaduan->nomor_pengaduan)

@section('content')
<div class="mb-4">
    <a href="{{ route('petugas.pengaduan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <div class="flex items-start justify-between mb-4 flex-wrap gap-2">
                <div>
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-1">{{ $pengaduan->nomor_pengaduan }}</p>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $pengaduan->judul }}</h2>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pengaduan->statusBadge() }}">{{ ucfirst($pengaduan->status) }}</span>
            </div>
            <div class="mb-4">
                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $pengaduan->deskripsi }}</p>
            </div>
            @if($pengaduan->foto)
            <img src="{{ Storage::url($pengaduan->foto) }}" class="max-h-52 rounded-xl object-cover border border-gray-100 dark:border-gray-800">
            @endif
            <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-800 grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-400">Pelanggan</p><p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pengaduan->pelanggan->nama_pelanggan }}</p></div>
                <div><p class="text-xs text-gray-400">Alamat</p><p class="text-gray-700 dark:text-gray-300 text-xs">{{ $pengaduan->pelanggan->alamat }}</p></div>
                <div><p class="text-xs text-gray-400">Jenis</p><p class="font-semibold capitalize text-gray-800 dark:text-gray-200">{{ $pengaduan->jenis }}</p></div>
                <div><p class="text-xs text-gray-400">Dilaporkan</p><p class="text-gray-700 dark:text-gray-300 text-xs">{{ $pengaduan->created_at->format('d/m/Y H:i') }}</p></div>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 dark:text-white mb-4">Update Status</h3>
            <form method="POST" action="{{ route('petugas.pengaduan.proses', $pengaduan) }}">
                @csrf
                <div class="space-y-2 mb-4">
                    @foreach(['diproses'=>['Tandai Diproses','amber'],'selesai'=>['Tandai Selesai','emerald']] as $val=>[$label,$color])
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $pengaduan->status === $val ? "border-{$color}-500 bg-{$color}-50 dark:bg-{$color}-900/20" : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                        <input type="radio" name="status" value="{{ $val }}" {{ $pengaduan->status === $val ? 'checked' : '' }}>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan (opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Catatan penanganan..."
                        class="w-full py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ $pengaduan->tanggapan }}</textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow transition-all text-sm">
                    Simpan Update
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
