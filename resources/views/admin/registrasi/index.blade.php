@extends('layouts.app')
@section('title','Pendaftaran Akun')
@section('page_title','Pendaftaran Akun')
@section('page_subtitle','Setujui atau tolak pendaftar baru')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-amber-200 dark:border-amber-800 shadow-sm bg-amber-50/50 dark:bg-amber-900/10">
        <p class="text-xs text-amber-600 dark:text-amber-400 mb-1">Menunggu Persetujuan</p>
        <p class="text-2xl font-extrabold text-amber-700 dark:text-amber-300">{{ $totalPending }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Pelanggan Pending</p>
        <p class="text-2xl font-extrabold text-brand-600 dark:text-brand-400">{{ $pelangganPending->count() }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Petugas Pending</p>
        <p class="text-2xl font-extrabold text-purple-600 dark:text-purple-400">{{ $petugasPending->count() }}</p>
    </div>
</div>

{{-- Pelanggan Pending --}}
@if($pelangganPending->count())
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center">
            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">Pendaftar Pelanggan ({{ $pelangganPending->count() }})</h3>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-800">
        @foreach($pelangganPending as $p)
        <div class="px-5 py-4" x-data="{ expand: false }">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($p->nama_pelanggan,0,2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $p->user->email }} · {{ $p->no_hp ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $p->alamat }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                        Pending
                    </span>
                    <button @click="expand = !expand"
                        class="px-4 py-2 text-xs font-semibold text-brand-600 bg-brand-50 dark:bg-brand-900/30 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-xl transition-all">
                        Tinjau
                    </button>
                </div>
            </div>

            {{-- Form Approve/Reject --}}
            <div x-show="expand" x-transition class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Approve --}}
                    <form method="POST" action="{{ route('admin.registrasi.approve', ['type'=>'pelanggan','id'=>$p->id]) }}"
                        class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl p-4 border border-emerald-100 dark:border-emerald-800">
                        @csrf
                        <h4 class="font-semibold text-emerald-800 dark:text-emerald-200 text-sm mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Setujui Pendaftaran
                        </h4>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Angka Meteran Awal</label>
                            <input type="number" name="meteran_awal" value="0" min="0"
                                class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Catatan (opsional)</label>
                            <textarea name="catatan" rows="2" placeholder="Catatan untuk pelanggan..."
                                class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none transition-all"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all text-sm flex items-center justify-center gap-2"
                            onclick="return confirm('Setujui pendaftaran {{ $p->nama_pelanggan }}?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Setujui & Aktifkan
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form method="POST" action="{{ route('admin.registrasi.reject', ['type'=>'pelanggan','id'=>$p->id]) }}"
                        class="bg-red-50 dark:bg-red-900/20 rounded-2xl p-4 border border-red-100 dark:border-red-800">
                        @csrf
                        <h4 class="font-semibold text-red-800 dark:text-red-200 text-sm mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tolak Pendaftaran
                        </h4>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-red-700 dark:text-red-300 mb-1">Alasan Penolakan *</label>
                            <textarea name="catatan" rows="4" required placeholder="Jelaskan alasan penolakan kepada pendaftar..."
                                class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 resize-none transition-all"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all text-sm flex items-center justify-center gap-2"
                            onclick="return confirm('Tolak pendaftaran {{ $p->nama_pelanggan }}?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Petugas Pending --}}
@if($petugasPending->count())
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="font-bold text-gray-800 dark:text-white">Pendaftar Petugas ({{ $petugasPending->count() }})</h3>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-800">
        @foreach($petugasPending as $p)
        <div class="px-5 py-4" x-data="{ expand: false }">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($p->nama_petugas,0,2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_petugas }}</p>
                        <p class="text-xs text-gray-400">{{ $p->user->email }} · {{ $p->no_hp ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $p->jabatan ?? 'Jabatan belum diisi' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Pending</span>
                    <button @click="expand = !expand"
                        class="px-4 py-2 text-xs font-semibold text-purple-600 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-xl transition-all">
                        Tinjau
                    </button>
                </div>
            </div>

            <div x-show="expand" x-transition class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <form method="POST" action="{{ route('admin.registrasi.approve', ['type'=>'petugas','id'=>$p->id]) }}"
                        class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl p-4 border border-emerald-100 dark:border-emerald-800">
                        @csrf
                        <h4 class="font-semibold text-emerald-800 dark:text-emerald-200 text-sm mb-3">✅ Setujui</h4>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-emerald-700 mb-1">Catatan (opsional)</label>
                            <textarea name="catatan" rows="3" class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none transition-all"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all text-sm"
                            onclick="return confirm('Setujui petugas {{ $p->nama_petugas }}?')">Setujui & Aktifkan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.registrasi.reject', ['type'=>'petugas','id'=>$p->id]) }}"
                        class="bg-red-50 dark:bg-red-900/20 rounded-2xl p-4 border border-red-100 dark:border-red-800">
                        @csrf
                        <h4 class="font-semibold text-red-800 dark:text-red-200 text-sm mb-3">❌ Tolak</h4>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-red-700 mb-1">Alasan Penolakan *</label>
                            <textarea name="catatan" rows="3" required class="w-full py-2 px-3 text-sm bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 resize-none transition-all"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all text-sm"
                            onclick="return confirm('Tolak petugas {{ $p->nama_petugas }}?')">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($totalPending === 0)
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm py-20 text-center">
    <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-gray-400 font-medium text-lg">Tidak ada pendaftaran menunggu</p>
    <p class="text-gray-300 dark:text-gray-600 text-sm mt-1">Semua pendaftaran sudah diproses</p>
</div>
@endif

{{-- Riwayat 7 hari --}}
@if($riwayat->count())
<div class="mt-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Riwayat Persetujuan (7 Hari Terakhir)</h3>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-800">
        @foreach($riwayat->take(10) as $item)
        @php
            $d = $item->data;
            $nama = $item->type === 'pelanggan' ? $d->nama_pelanggan : $d->nama_petugas;
        @endphp
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg {{ $d->status_registrasi==='approved' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40' }} flex items-center justify-center">
                    @if($d->status_registrasi==='approved')
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $nama }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $item->type }} · {{ $d->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $d->status_registrasi==='approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                {{ $d->status_registrasi==='approved' ? 'Disetujui' : 'Ditolak' }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
