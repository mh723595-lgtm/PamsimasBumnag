{{-- resources/views/admin/petugas/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Petugas')
@section('page_title','Manajemen Petugas')
@section('page_subtitle','Kelola data petugas lapangan dan penugasan jorong')

@section('content')

{{-- Alert box --}}
<div id="alert-box" class="hidden mb-4">
    <div id="alert-content" class="flex items-center gap-3 p-4 rounded-xl border text-sm font-medium">
        <span id="alert-message"></span>
    </div>
</div>

@if(session('success'))
<div class="mb-4 flex items-center gap-3 p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300 text-sm font-medium">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex gap-3 items-end">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Cari</button>
        </form>
        <a href="{{ route('admin.petugas.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Petugas
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jabatan</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jorong Ditugaskan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($petugas as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($p->nama_petugas,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_petugas }}</p>
                                <p class="text-xs text-gray-400">{{ $p->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $p->jabatan ?? '—' }}</p>
                        <p class="text-xs font-mono text-gray-400">{{ $p->nik ?? '' }}</p>
                    </td>
                    <td class="px-3 py-3.5">
                        @php $aktifAssign = $p->assignPetugas->where('aktif', true); @endphp
                        @if($aktifAssign->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($aktifAssign as $a)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                    {{ $a->jorong->nama_jorong ?? '?' }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-800">Belum diassign</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-500">{{ $p->no_hp ?? '—' }}</td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }} capitalize">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Lihat Pelanggan --}}
                            <button
                                onclick="openModalPelanggan({{ $p->id }}, '{{ addslashes($p->nama_petugas) }}', '{{ addslashes($p->jabatan ?? '') }}')"
                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all"
                                title="Lihat Pelanggan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                            {{-- Assign Jorong --}}
                            <button
                                onclick="openAssignJorong({{ $p->id }}, '{{ addslashes($p->nama_petugas) }}')"
                                class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all"
                                title="Assign Jorong">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.petugas.edit', $p) }}"
                                class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all"
                                title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('admin.petugas.destroy', $p) }}"
                                onsubmit="return confirm('Hapus petugas {{ addslashes($p->nama_petugas) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">Tidak ada petugas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($petugas->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">{{ $petugas->links() }}</div>
    @endif
</div>

{{-- ═══ MODAL ASSIGN JORONG ═══ --}}
<div id="modal-assign-jorong" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-emerald-500">
            <div>
                <h3 class="font-semibold text-white text-sm">Assign Jorong</h3>
                <p class="text-emerald-100 text-xs mt-0.5" id="modal-assign-subtitle">—</p>
            </div>
            <button onclick="closeAssignModal()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="px-6 pt-5 pb-3 border-b border-gray-100 dark:border-gray-800">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tambah Jorong Baru</p>
            <div class="flex gap-2">
                <select id="modal-jorong-id"
                    class="flex-1 px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 text-gray-700 dark:text-gray-300">
                    <option value="">— Pilih jorong —</option>
                    @foreach($jorongList as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jorong }}</option>
                    @endforeach
                </select>
                <select id="modal-periode"
                    class="w-36 px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 text-gray-700 dark:text-gray-300">
                    <option value="permanen">Permanen</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ \App\Services\TagihanService::namaBulan($m) . ' ' . now()->year }}">
                        {{ \App\Services\TagihanService::namaBulan($m) }} {{ now()->year }}
                    </option>
                    @endfor
                </select>
                <button onclick="simpanAssignJorong()"
                    class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
                    + Tambah
                </button>
            </div>
        </div>
        <div class="px-6 py-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Jorong Saat Ini</p>
            <div id="modal-jorong-list" class="space-y-2 max-h-56 overflow-y-auto">
                <p class="text-center text-gray-400 text-sm py-4">Memuat...</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button onclick="closeAssignModal()"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ═══ MODAL DAFTAR PELANGGAN ═══ --}}
<div id="modal-pelanggan" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 flex flex-col" style="max-height:88vh">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 bg-blue-600 rounded-t-2xl flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm" id="mp-nama">—</h3>
                    <p class="text-blue-100 text-xs mt-0.5" id="mp-jabatan">—</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="mp-badge-total"
                    class="hidden items-center gap-1 px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span id="mp-total-text">0</span> Pelanggan
                </span>
                <button onclick="closePelangganModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search bar --}}
        <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
            <div class="relative">
                <input type="text" id="mp-search" placeholder="Cari nama atau nomor pelanggan…"
                    oninput="filterPelangganModal(this.value)"
                    class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Daftar pelanggan --}}
        <div class="overflow-y-auto flex-1 px-6 py-4" id="mp-list">
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-8 h-8 mb-3 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <p class="text-sm">Memuat data...</p>
            </div>
        </div>

        {{-- Pagination --}}
        <div id="mp-pagination" class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between flex-shrink-0 hidden">
            <p class="text-xs text-gray-400" id="mp-pagination-info"></p>
            <div class="flex items-center gap-1" id="mp-pagination-buttons"></div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between flex-shrink-0">
            <p class="text-xs text-gray-400" id="mp-footer-info">—</p>
            <button onclick="closePelangganModal()"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ═══ MODAL DETAIL SATU PELANGGAN ═══ --}}
<div id="modal-detail-pelanggan-petugas" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 bg-blue-600">
            <div>
                <h3 class="font-semibold text-white" id="pdp-nama">-</h3>
                <p class="text-blue-200 text-xs mt-0.5" id="pdp-nomor">-</p>
            </div>
            <button onclick="tutupDetailPelangganPetugas()" class="text-white/80 hover:text-white text-lg leading-none">✕</button>
        </div>

        {{-- Status Badge --}}
        <div class="px-6 pt-4 pb-2">
            <span id="pdp-status-badge" class="px-2.5 py-1 rounded-full text-xs font-semibold"></span>
        </div>

        {{-- Detail Grid --}}
        <div class="px-6 pb-4 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">No. KTP</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-ktp">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">No. HP</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-hp">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Jorong</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-jorong">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">RT/RW</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-rtrw">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Desa / Kelurahan</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-desa">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Kecamatan</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-kecamatan">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Kabupaten</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-kabupaten">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Provinsi</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-provinsi">-</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-400 mb-0.5">Alamat Lengkap</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-alamat">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">No. Meteran</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-meteran">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Meteran Awal (m³)</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-meteran-awal">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Tanggal Daftar</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-tgl-daftar">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Petugas</p>
                <p class="font-medium text-gray-800 dark:text-white" id="pdp-petugas">-</p>
            </div>
        </div>

        {{-- Maps Link --}}
        <div id="pdp-maps-wrapper" class="px-6 pb-4 hidden">
            <a id="pdp-maps-link" href="#" target="_blank"
                class="inline-flex items-center gap-2 text-xs text-blue-600 hover:text-blue-700 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Lihat di Google Maps
            </a>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button onclick="tutupDetailPelangganPetugas()"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPetugasId = null;

const CSRF          = '{{ csrf_token() }}';
const ROUTE_STORE   = '{{ route("admin.assign-petugas.store") }}';
const ROUTE_TOGGLE  = '/admin/assign-petugas/:id/toggle';
const ROUTE_DESTROY = '/admin/assign-petugas/:id';
const ROUTE_DETAIL  = '/admin/assign-petugas/petugas/:id';

function showAlert(type, msg) {
    const box     = document.getElementById('alert-box');
    const content = document.getElementById('alert-content');
    const styles  = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300',
        danger:  'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300',
    };
    content.className = `flex items-center gap-3 p-4 rounded-xl border text-sm font-medium ${styles[type]}`;
    document.getElementById('alert-message').textContent = msg;
    box.classList.remove('hidden');
    setTimeout(() => box.classList.add('hidden'), 4000);
}

// ══════════════════════════════════════════════
// MODAL ASSIGN JORONG
// ══════════════════════════════════════════════
function openAssignJorong(petugasId, namaPetugas) {
    currentPetugasId = petugasId;
    document.getElementById('modal-assign-subtitle').textContent = namaPetugas;
    document.getElementById('modal-jorong-id').value  = '';
    document.getElementById('modal-periode').value    = 'permanen';
    document.getElementById('modal-assign-jorong').classList.remove('hidden');
    fetchAssigns();
}

function closeAssignModal() {
    document.getElementById('modal-assign-jorong').classList.add('hidden');
    currentPetugasId = null;
}

function fetchAssigns() {
    fetch(ROUTE_DETAIL.replace(':id', currentPetugasId), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => renderJorongList(data))
    .catch(() => {
        document.getElementById('modal-jorong-list').innerHTML =
            '<p class="text-center text-red-400 text-sm py-4">Gagal memuat data.</p>';
    });
}

function renderJorongList(data) {
    const list = document.getElementById('modal-jorong-list');

    if (!data.assigns || data.assigns.length === 0) {
        list.innerHTML = '<div class="text-center py-6 text-gray-400"><p class="text-2xl mb-1">📍</p><p class="text-xs">Belum ada jorong yang diassign</p></div>';
        return;
    }

    list.innerHTML = data.assigns.map(a => `
        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors" id="assign-row-${a.id}">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">${a.jorong}</p>
                    <p class="text-xs text-gray-400">${a.periode}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="toggleAssign(${a.id}, this)"
                    data-aktif="${a.aktif ? '1' : '0'}"
                    class="px-2 py-1 rounded-full text-xs font-semibold transition-colors ${a.aktif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800'}">
                    ${a.aktif ? 'Aktif' : 'Nonaktif'}
                </button>
                <button onclick="hapusAssign(${a.id})"
                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    `).join('');
}

function simpanAssignJorong() {
    const jorongId = document.getElementById('modal-jorong-id').value;
    const periode  = document.getElementById('modal-periode').value;
    if (!jorongId) { showAlert('danger', 'Pilih jorong terlebih dahulu!'); return; }

    fetch(ROUTE_STORE, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ petugas_id: currentPetugasId, jorong_id: jorongId, periode: periode })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            document.getElementById('modal-jorong-id').value = '';
            fetchAssigns();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message ?? 'Gagal menyimpan.');
        }
    })
    .catch(() => showAlert('danger', 'Terjadi kesalahan.'));
}

function toggleAssign(id, btn) {
    fetch(ROUTE_TOGGLE.replace(':id', id), {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const aktif    = data.aktif;
            btn.dataset.aktif = aktif ? '1' : '0';
            btn.textContent   = aktif ? 'Aktif' : 'Nonaktif';
            btn.className     = `px-2 py-1 rounded-full text-xs font-semibold transition-colors ${aktif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800'}`;
        }
    });
}

function hapusAssign(id) {
    if (!confirm('Hapus assign jorong ini?')) return;
    fetch(ROUTE_DESTROY.replace(':id', id), {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('assign-row-' + id)?.remove();
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        }
    });
}

document.getElementById('modal-assign-jorong').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});

// ══════════════════════════════════════════════
// MODAL DAFTAR PELANGGAN (dengan Pagination)
// ══════════════════════════════════════════════
let _mpPetugasId  = null;
let _mpPage       = 1;
let _mpLastPage   = 1;
let _mpSearchMode = false;
let allPelangganData = [];

function openModalPelanggan(petugasId, nama, jabatan) {
    _mpPetugasId  = petugasId;
    _mpPage       = 1;
    _mpSearchMode = false;
    allPelangganData = [];

    document.getElementById('mp-nama').textContent    = nama;
    document.getElementById('mp-jabatan').textContent = jabatan || '—';
    document.getElementById('mp-search').value        = '';
    document.getElementById('mp-badge-total').classList.add('hidden');
    document.getElementById('mp-footer-info').textContent = '—';
    document.getElementById('mp-pagination').classList.add('hidden');
    document.getElementById('mp-list').innerHTML = `
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-8 h-8 mb-3 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-sm">Memuat data...</p>
        </div>`;

    document.getElementById('modal-pelanggan').classList.remove('hidden');
    muatPelangganModal(petugasId, 1);
}

function muatPelangganModal(petugasId, page) {
    document.getElementById('mp-list').innerHTML = `
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-8 h-8 mb-3 animate-spin text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-sm">Memuat data...</p>
        </div>`;

    fetch(ROUTE_DETAIL.replace(':id', petugasId) + '?page=' + page, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        allPelangganData = data.pelanggan ?? [];
        const pg = data.pagination;
        _mpPage     = pg.current_page;
        _mpLastPage = pg.last_page;

        // Badge total
        const badge = document.getElementById('mp-badge-total');
        document.getElementById('mp-total-text').textContent = pg.total;
        badge.classList.remove('hidden');
        badge.classList.add('flex');

        // Counter footer
        const start = (pg.current_page - 1) * pg.per_page + 1;
        const end   = Math.min(pg.current_page * pg.per_page, pg.total);
        document.getElementById('mp-footer-info').textContent =
            pg.total > 0
                ? `Menampilkan ${start}–${end} dari ${pg.total} pelanggan aktif`
                : 'Total: 0 pelanggan aktif';

        renderPelangganModal(allPelangganData, pg.total);
        renderPaginationModal(pg);
    })
    .catch(() => {
        document.getElementById('mp-list').innerHTML = `
            <div class="flex flex-col items-center py-16 text-red-400">
                <p class="text-sm font-medium">Gagal memuat data pelanggan.</p>
            </div>`;
    });
}

function renderPelangganModal(list, total) {
    if (list.length === 0) {
        document.getElementById('mp-list').innerHTML = `
            <div class="flex flex-col items-center py-16 text-gray-400">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada pelanggan</p>
                <p class="text-xs text-gray-400 mt-1">Assign pelanggan ke petugas ini terlebih dahulu</p>
            </div>`;
        return;
    }

    // Kelompokkan per jorong
    const grouped = {};
    list.forEach(p => {
        const key = p.jorong || '—';
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(p);
    });

    let html = '';
    Object.entries(grouped).forEach(([jorong, pelangganList]) => {
        html += `
        <div class="mb-4">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">${jorong}</span>
                <span class="text-xs text-gray-400">(${pelangganList.length})</span>
                <div class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></div>
            </div>
            <div class="space-y-2">`;

        pelangganList.forEach(p => {
            html += `
                <div class="mp-item flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800
                            hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-colors cursor-pointer group"
                     data-search="${(p.nama + ' ' + p.nomor).toLowerCase()}"
                     onclick="lihatDetailPelangganPetugas(${p.id})"
                     title="Klik untuk lihat detail pelanggan">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        ${p.nama.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate group-hover:text-blue-600 transition-colors">${p.nama}</p>
                        <p class="text-xs text-gray-400 truncate">${p.alamat}</p>
                    </div>
                    <div class="text-right flex-shrink-0 flex items-center gap-2">
                        <div>
                            <p class="text-xs font-mono font-semibold text-brand-600 dark:text-brand-400">${p.nomor}</p>
                            <p class="text-xs text-gray-400">${p.no_hp !== '-' ? p.no_hp : ''}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>`;
        });

        html += `</div></div>`;
    });

    document.getElementById('mp-list').innerHTML = html;
}

function renderPaginationModal(pg) {
    const wrapper = document.getElementById('mp-pagination');
    const info    = document.getElementById('mp-pagination-info');
    const buttons = document.getElementById('mp-pagination-buttons');

    if (pg.last_page <= 1) {
        wrapper.classList.add('hidden');
        return;
    }

    wrapper.classList.remove('hidden');
    info.textContent = `Halaman ${pg.current_page} dari ${pg.last_page}`;

    let html = '';

    // Prev
    html += `<button onclick="gantiHalamanModal(${pg.current_page - 1})"
        ${pg.current_page <= 1 ? 'disabled' : ''}
        class="w-8 h-8 rounded-lg text-xs font-medium border transition-colors
               ${pg.current_page <= 1
                   ? 'border-gray-100 text-gray-300 cursor-not-allowed'
                   : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-blue-400 hover:text-blue-600'}">‹</button>`;

    // Nomor halaman
    mpPaginationRange(pg.current_page, pg.last_page, 5).forEach(n => {
        if (n === '...') {
            html += `<span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">…</span>`;
        } else {
            const active = n === pg.current_page;
            html += `<button onclick="gantiHalamanModal(${n})"
                class="w-8 h-8 rounded-lg text-xs font-medium border transition-colors
                       ${active ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-blue-400 hover:text-blue-600'}">
                ${n}
            </button>`;
        }
    });

    // Next
    html += `<button onclick="gantiHalamanModal(${pg.current_page + 1})"
        ${pg.current_page >= pg.last_page ? 'disabled' : ''}
        class="w-8 h-8 rounded-lg text-xs font-medium border transition-colors
               ${pg.current_page >= pg.last_page
                   ? 'border-gray-100 text-gray-300 cursor-not-allowed'
                   : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-blue-400 hover:text-blue-600'}">›</button>`;

    buttons.innerHTML = html;
}

function mpPaginationRange(current, last, maxButtons) {
    if (last <= maxButtons) return Array.from({ length: last }, (_, i) => i + 1);
    const half  = Math.floor(maxButtons / 2);
    let start   = Math.max(1, current - half);
    let end     = Math.min(last, start + maxButtons - 1);
    if (end - start + 1 < maxButtons) start = Math.max(1, end - maxButtons + 1);
    const pages = [];
    if (start > 1) { pages.push(1); if (start > 2) pages.push('...'); }
    for (let i = start; i <= end; i++) pages.push(i);
    if (end < last) { if (end < last - 1) pages.push('...'); pages.push(last); }
    return pages;
}

function gantiHalamanModal(page) {
    if (page < 1 || page > _mpLastPage || !_mpPetugasId) return;
    _mpPage = page;
    muatPelangganModal(_mpPetugasId, page);
}

function filterPelangganModal(keyword) {
    const q     = keyword.toLowerCase().trim();
    const items = document.querySelectorAll('#mp-list .mp-item');
    let visible = 0;

    items.forEach(el => {
        const match = !q || el.dataset.search.includes(q);
        el.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('mp-footer-info').textContent = q
        ? `${visible} hasil pencarian`
        : `Menampilkan ${allPelangganData.length} pelanggan aktif`;
}

function closePelangganModal() {
    document.getElementById('modal-pelanggan').classList.add('hidden');
    _mpPetugasId = null;
    allPelangganData = [];
}

document.getElementById('modal-pelanggan').addEventListener('click', function(e) {
    if (e.target === this) closePelangganModal();
});

// ══════════════════════════════════════════════
// MODAL DETAIL SATU PELANGGAN
// ══════════════════════════════════════════════
function tutupDetailPelangganPetugas() {
    document.getElementById('modal-detail-pelanggan-petugas').classList.add('hidden');
}

function lihatDetailPelangganPetugas(pelangganId) {
    const fields = ['nama','nomor','ktp','hp','jorong','rtrw','desa','kecamatan',
                    'kabupaten','provinsi','alamat','meteran','meteran-awal','tgl-daftar','petugas'];
    fields.forEach(f => {
        const el = document.getElementById('pdp-' + f);
        if (el) el.textContent = '...';
    });
    document.getElementById('pdp-maps-wrapper').classList.add('hidden');
    document.getElementById('pdp-status-badge').textContent = '';
    document.getElementById('modal-detail-pelanggan-petugas').classList.remove('hidden');

    fetch(`/admin/assign-petugas/pelanggan/${pelangganId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(p => {
        document.getElementById('pdp-nama').textContent         = p.nama;
        document.getElementById('pdp-nomor').textContent        = 'No. ' + p.nomor;
        document.getElementById('pdp-ktp').textContent          = p.no_ktp;
        document.getElementById('pdp-hp').textContent           = p.no_hp;
        document.getElementById('pdp-jorong').textContent       = p.jorong;
        document.getElementById('pdp-rtrw').textContent         = p.rt_rw;
        document.getElementById('pdp-desa').textContent         = p.desa;
        document.getElementById('pdp-kecamatan').textContent    = p.kecamatan;
        document.getElementById('pdp-kabupaten').textContent    = p.kabupaten;
        document.getElementById('pdp-provinsi').textContent     = p.provinsi;
        document.getElementById('pdp-alamat').textContent       = p.alamat;
        document.getElementById('pdp-meteran').textContent      = p.nomor_meteran;
        document.getElementById('pdp-meteran-awal').textContent = p.meteran_awal + ' m³';
        document.getElementById('pdp-tgl-daftar').textContent   = p.tanggal_daftar;
        document.getElementById('pdp-petugas').textContent      = p.petugas;

        const badge = document.getElementById('pdp-status-badge');
        badge.textContent = '● ' + (p.status === 'aktif' ? 'Aktif' : p.status);
        badge.className   = `px-2.5 py-1 rounded-full text-xs font-semibold ${
            p.status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'
        }`;

        if (p.maps_url) {
            document.getElementById('pdp-maps-link').href = p.maps_url;
            document.getElementById('pdp-maps-wrapper').classList.remove('hidden');
        }
    })
    .catch(() => {
        document.getElementById('pdp-nama').textContent = 'Gagal memuat data';
    });
}

document.getElementById('modal-detail-pelanggan-petugas').addEventListener('click', function(e) {
    if (e.target === this) tutupDetailPelangganPetugas();
});
</script>
@endpush