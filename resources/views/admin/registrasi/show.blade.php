@extends('layouts.app')
@section('title','Detail Pendaftar')
@section('page_title','Detail Pendaftar')
@section('page_subtitle', ucfirst($type) . ' — ' . ($type==='pelanggan' ? $data->nama_pelanggan : $data->nama_petugas))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.registrasi.index') }}"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Info Pendaftar --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($type==='pelanggan' ? $data->nama_pelanggan : $data->nama_petugas, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $type==='pelanggan' ? $data->nama_pelanggan : $data->nama_petugas }}
                        </h2>
                        <p class="text-gray-400 text-sm">{{ $data->user->email }}</p>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 capitalize">
                            {{ $data->status_registrasi }} · {{ ucfirst($type) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Alamat</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->alamat }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. HP</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->no_hp ?? '-' }}</p>
                    </div>

                    @if($type === 'pelanggan')
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Desa/Kelurahan</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->desa ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Kecamatan</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->kecamatan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">No. KTP</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->no_ktp ?? '-' }}</p>
                    </div>
                    @endif

                    @if($type === 'petugas')
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">NIP</p>
                        <p class="text-gray-700 dark:text-gray-300 font-mono">{{ $data->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Jabatan</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->jabatan ?? '-' }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Waktu Daftar</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $data->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Status Akun</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $data->user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                            {{ $data->user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve/Reject --}}
    @if($data->status_registrasi === 'pending')
    <div class="space-y-4">
        {{-- Approve --}}
        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800 shadow-sm p-5">
            <h3 class="font-bold text-emerald-800 dark:text-emerald-200 text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Setujui Pendaftaran
            </h3>
            <form method="POST" action="{{ route('admin.registrasi.approve', ['type'=>$type,'id'=>$data->id]) }}" class="space-y-3">
                @csrf
                @if($type === 'pelanggan')
                <div>
                    <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Angka Meteran Awal</label>
                    <input type="number" name="meteran_awal" value="0" min="0"
                        class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Catatan (opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Catatan untuk pendaftar..."
                        class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none transition-all"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all text-sm flex items-center justify-center gap-2"
                    onclick="return confirm('Setujui dan aktifkan akun ini?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui & Aktifkan
                </button>
            </form>
        </div>

        {{-- Reject --}}
        <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-100 dark:border-red-800 shadow-sm p-5">
            <h3 class="font-bold text-red-800 dark:text-red-200 text-sm mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tolak Pendaftaran
            </h3>
            <form method="POST" action="{{ route('admin.registrasi.reject', ['type'=>$type,'id'=>$data->id]) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-red-700 dark:text-red-300 mb-1">Alasan Penolakan *</label>
                    <textarea name="catatan" rows="4" required
                        placeholder="Jelaskan alasan penolakan secara jelas kepada pendaftar..."
                        class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 resize-none transition-all"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all text-sm flex items-center justify-center gap-2"
                    onclick="return confirm('Tolak pendaftaran ini?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
            </form>
        </div>
    </div>
    @else
    {{-- Already processed --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">Status Persetujuan</h3>
        <div class="p-4 {{ $data->status_registrasi==='approved' ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800' }} rounded-xl">
            <p class="font-semibold text-sm {{ $data->status_registrasi==='approved' ? 'text-emerald-800 dark:text-emerald-200' : 'text-red-800 dark:text-red-200' }} mb-1">
                {{ $data->status_registrasi==='approved' ? '✅ Disetujui' : '❌ Ditolak' }}
            </p>
            @if($data->catatan_registrasi)
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $data->catatan_registrasi }}</p>
            @endif
            @if($data->approved_at)
            <p class="text-xs text-gray-400 mt-1">{{ $data->approved_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
