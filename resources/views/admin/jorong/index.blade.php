@extends('layouts.app')
@section('title', 'Kelola Jorong')
@section('page_title', 'Kelola Jorong')
@section('page_subtitle', 'Tambah dan kelola data jorong di Nagari Bayua')

@section('content')

    <div id="alert-box" class="hidden mb-4">
        <div id="alert-content" class="flex items-center gap-3 p-4 rounded-xl border text-sm font-medium">
            <span id="alert-message"></span>
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="font-semibold text-gray-800 dark:text-white">Daftar Jorong</h2>
            <button onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Jorong
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Nama Jorong</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Wilayah</th>
                        {{-- <th class="px-4 py-3 text-left">Keterangan</th> --}}
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @php $grouped = $jorong->groupBy('kabupaten');
                    $no = 1; @endphp

                    @forelse($grouped as $kabupaten => $items)
                        @if($kabupaten)
                            <tr class="bg-emerald-50/60 dark:bg-emerald-900/10">
                                <td colspan="7" class="px-4 py-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span
                                            class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                                            {{ $kabupaten }}
                                            @if($items->first()->provinsi)
                                                <span class="font-normal text-gray-400 normal-case tracking-normal">—
                                                    {{ $items->first()->provinsi }}</span>
                                            @endif
                                        </span>
                                        <button
                                            onclick="bukaModalTambahDiWilayah('{{ addslashes($items->first()->provinsi) }}','{{ addslashes($kabupaten) }}','{{ addslashes($items->first()->kecamatan) }}','{{ addslashes($items->first()->desa) }}','{{ addslashes($items->first()->nagari) }}')"
                                            class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah di sini
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        @foreach($items as $j)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $no++ }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        @if($kabupaten)<span
                                        class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>@endif
                                        {{ $j->nama_jorong }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-mono">{{ $j->kode_jorong ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    @if($j->nagari || $j->desa)
                                        @if($j->nagari)
                                            <span class="text-gray-600 dark:text-gray-400">Nagari {{ $j->nagari }}</span>
                                        @endif

                                        @if($j->desa)
                                            <span class="text-gray-400"> · {{ $j->desa }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                {{-- <td class="px-4 py-3 text-gray-500 text-xs">{{ $j->keterangan ?? '-' }}</td> --}}
                                <td class="px-4 py-3">
                                    <button onclick="toggleAktif({{ $j->id }}, this)"
                                        class="px-2 py-1 rounded-full text-xs font-semibold transition-colors {{ $j->aktif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                                        {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            onclick="editJorong({{ $j->id }},'{{ addslashes($j->nama_jorong) }}','{{ addslashes($j->kode_jorong) }}','{{ addslashes($j->provinsi) }}','{{ addslashes($j->kabupaten) }}','{{ addslashes($j->kecamatan) }}','{{ addslashes($j->desa) }}','{{ addslashes($j->nagari) }}')"
                                            class="px-3 py-1.5 text-xs font-medium border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">Edit</button>
                                        <button onclick="hapusJorong({{ $j->id }})"
                                            class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-500 hover:border-red-400 hover:text-red-500 rounded-lg transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                <p class="text-3xl mb-2">🗺️</p>
                                <p class="text-sm">Belum ada jorong. Tambahkan jorong pertama!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
    Modal Tambah Jorong
    ════════════════════════════════════════════ --}}
    <div id="modal-tambah"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden max-h-[92vh] flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-emerald-500 flex-shrink-0">
                <div>
                    <h3 class="font-semibold text-white">Tambah Jorong Baru</h3>
                    <p class="text-emerald-100 text-xs mt-0.5">Pilih wilayah, lalu tambahkan jorong</p>
                </div>
                <button onclick="tutupModalTambah()" class="text-white/80 hover:text-white">✕</button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- Step 1: Wilayah --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center font-bold">1</span>
                        Pilih Wilayah
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Provinsi</label>
                            <select id="tambah-provinsi" onchange="loadKabupaten('tambah')"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                <option value="">— Pilih Provinsi —</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kabupaten /
                                Kota</label>
                            <select id="tambah-kabupaten" onchange="loadKecamatan('tambah')" disabled
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 disabled:opacity-50">
                                <option value="">— Pilih Kabupaten —</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kecamatan</label>
                            <select id="tambah-kecamatan" onchange="loadDesa('tambah')" disabled
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 disabled:opacity-50">
                                <option value="">— Pilih Kecamatan —</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Desa /
                                Kelurahan</label>
                            <select id="tambah-desa" disabled
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 disabled:opacity-50">
                                <option value="">— Pilih Desa —</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nagari <span
                                class="font-normal text-gray-400">(opsional)</span></label>
                        <input type="text" id="tambah-nagari" placeholder="Contoh: Nagari Bayua"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-gray-800">

                {{-- Step 2: Daftar Jorong --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span
                            class="w-5 h-5 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Daftar Jorong di Wilayah Ini
                    </p>

                    {{-- List jorong yang akan ditambah --}}
                    <div id="jorong-list" class="space-y-2 mb-3">
                        {{-- Item pertama (tidak bisa dihapus) --}}
                        <div
                            class="jorong-item flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 border border-gray-200 dark:border-gray-700">
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                <input type="text" placeholder="Nama Jorong *"
                                    class="jorong-nama px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
                                <input type="text" placeholder="Kode (otomatis)"
                                    class="jorong-kode px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
                            </div>
                            <div class="w-8 flex-shrink-0"></div>
                        </div>
                    </div>

                    {{-- Tombol tambah jorong baru --}}
                    <button type="button" onclick="tambahBariJorong()"
                        class="w-full py-2.5 border-2 border-dashed border-emerald-300 dark:border-emerald-700 text-emerald-600 dark:text-emerald-400 text-sm font-semibold rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Tambah Jorong Lagi
                    </button>
                </div>

            </div>

            {{-- Footer --}}
            <div
                class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end flex-shrink-0 bg-gray-50 dark:bg-gray-800/50">
                <button onclick="tutupModalTambah()"
                    class="px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                <button onclick="simpanTambah()" id="btnSimpan"
                    class="px-5 py-2 rounded-xl text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Semua
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ Modal Edit ═══ --}}
    <div id="modal-edit"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 bg-blue-500 flex-shrink-0">
                <h3 class="font-semibold text-white">Edit Jorong</h3>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-white/80 hover:text-white">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
                <input type="hidden" id="edit-id">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Jorong <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="edit-nama"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kode Jorong</label>
                    <input type="text" id="edit-kode"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <hr class="border-gray-100 dark:border-gray-800">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Wilayah</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Provinsi</label>
                        <select id="edit-provinsi" onchange="loadKabupaten('edit')"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">— Pilih Provinsi —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kabupaten /
                            Kota</label>
                        <select id="edit-kabupaten" onchange="loadKecamatan('edit')"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">— Pilih Kabupaten —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kecamatan</label>
                        <select id="edit-kecamatan" onchange="loadDesa('edit')"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">— Pilih Kecamatan —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Desa /
                            Kelurahan</label>
                        <select id="edit-desa"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">— Pilih Desa —</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nagari</label>
                    <input type="text" id="edit-nagari"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                {{-- <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Keterangan</label>
                    <input type="text" id="edit-keterangan"
                        class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div> --}}
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end flex-shrink-0">
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-gray-100 transition-colors">Batal</button>
                <button onclick="simpanEdit()"
                    class="px-4 py-2 rounded-xl text-sm font-semibold bg-blue-500 hover:bg-blue-600 text-white transition-colors">Simpan
                    Perubahan</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        const API = 'https://www.emsifa.com/api-wilayah-indonesia/api';

        // ── Alert ──────────────────────────────────────────────────
        function showAlert(type, message) {
            const box = document.getElementById('alert-box');
            const content = document.getElementById('alert-content');
            const styles = { success: 'bg-emerald-50 border-emerald-200 text-emerald-800', danger: 'bg-red-50 border-red-200 text-red-800' };
            content.className = `flex items-center gap-3 p-4 rounded-xl border text-sm font-medium ${styles[type]}`;
            document.getElementById('alert-message').textContent = message;
            box.classList.remove('hidden');
            setTimeout(() => box.classList.add('hidden'), 4000);
        }

        // ── Wilayah API ────────────────────────────────────────────
        async function loadProvinsi(prefix, selectedVal = '') {
            const sel = document.getElementById(`${prefix}-provinsi`);
            sel.innerHTML = '<option value="">Memuat...</option>';
            try {
                const data = await fetch(`${API}/provinces.json`).then(r => r.json());
                sel.innerHTML = '<option value="">— Pilih Provinsi —</option>' +
                    data.map(p => `<option value="${p.name}" data-id="${p.id}" ${p.name === selectedVal ? 'selected' : ''}>${p.name}</option>`).join('');
                if (selectedVal) await loadKabupaten(prefix);
            } catch (e) {
                sel.innerHTML = '<option value="">Gagal memuat — cek koneksi</option>';
            }
        }

        async function loadKabupaten(prefix, selectedVal = '') {
            const provSel = document.getElementById(`${prefix}-provinsi`);
            const kabSel = document.getElementById(`${prefix}-kabupaten`);
            const opt = provSel.querySelector(`option[value="${provSel.value}"]`);
            if (!opt?.dataset.id) { kabSel.innerHTML = '<option value="">— Pilih Kabupaten —</option>'; kabSel.disabled = true; resetBelow(prefix, 'kabupaten'); return; }
            kabSel.disabled = false;
            kabSel.innerHTML = '<option value="">Memuat...</option>';
            try {
                const data = await fetch(`${API}/regencies/${opt.dataset.id}.json`).then(r => r.json());
                kabSel.innerHTML = '<option value="">— Pilih Kabupaten —</option>' +
                    data.map(k => `<option value="${k.name}" data-id="${k.id}" ${k.name === selectedVal ? 'selected' : ''}>${k.name}</option>`).join('');
                resetBelow(prefix, 'kabupaten');
                if (selectedVal) await loadKecamatan(prefix);
            } catch (e) { kabSel.innerHTML = '<option value="">Gagal memuat</option>'; }
        }

        async function loadKecamatan(prefix, selectedVal = '') {
            const kabSel = document.getElementById(`${prefix}-kabupaten`);
            const kecSel = document.getElementById(`${prefix}-kecamatan`);
            const opt = kabSel.querySelector(`option[value="${kabSel.value}"]`);
            if (!opt?.dataset.id) { kecSel.innerHTML = '<option value="">— Pilih Kecamatan —</option>'; kecSel.disabled = true; resetBelow(prefix, 'kecamatan'); return; }
            kecSel.disabled = false;
            kecSel.innerHTML = '<option value="">Memuat...</option>';
            try {
                const data = await fetch(`${API}/districts/${opt.dataset.id}.json`).then(r => r.json());
                kecSel.innerHTML = '<option value="">— Pilih Kecamatan —</option>' +
                    data.map(k => `<option value="${k.name}" data-id="${k.id}" ${k.name === selectedVal ? 'selected' : ''}>${k.name}</option>`).join('');
                resetBelow(prefix, 'kecamatan');
                if (selectedVal) await loadDesa(prefix);
            } catch (e) { kecSel.innerHTML = '<option value="">Gagal memuat</option>'; }
        }

        async function loadDesa(prefix, selectedVal = '') {
            const kecSel = document.getElementById(`${prefix}-kecamatan`);
            const desaSel = document.getElementById(`${prefix}-desa`);
            const opt = kecSel.querySelector(`option[value="${kecSel.value}"]`);
            if (!opt?.dataset.id) { desaSel.innerHTML = '<option value="">— Pilih Desa —</option>'; desaSel.disabled = true; return; }
            desaSel.disabled = false;
            desaSel.innerHTML = '<option value="">Memuat...</option>';
            try {
                const data = await fetch(`${API}/villages/${opt.dataset.id}.json`).then(r => r.json());
                desaSel.innerHTML = '<option value="">— Pilih Desa —</option>' +
                    data.map(d => `<option value="${d.name}" ${d.name === selectedVal ? 'selected' : ''}>${d.name}</option>`).join('');
                if (selectedVal) desaSel.value = selectedVal;
            } catch (e) { desaSel.innerHTML = '<option value="">Gagal memuat</option>'; }
        }

        function resetBelow(prefix, level) {
            if (level === 'kabupaten') {
                const kec = document.getElementById(`${prefix}-kecamatan`);
                kec.innerHTML = '<option value="">— Pilih Kecamatan —</option>'; kec.disabled = true;
            }
            const desa = document.getElementById(`${prefix}-desa`);
            desa.innerHTML = '<option value="">— Pilih Desa —</option>'; desa.disabled = true;
        }

        // ── Baris Jorong dinamis ────────────────────────────────────
        function tambahBariJorong() {
            const list = document.getElementById('jorong-list');
            const div = document.createElement('div');
            div.className = 'jorong-item flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 border border-gray-200 dark:border-gray-700';
            div.innerHTML = `
            <div class="flex-1 grid grid-cols-2 gap-2">
                <input type="text" placeholder="Nama Jorong *"
                    class="jorong-nama px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
                <input type="text" placeholder="Kode (otomatis)"
                    class="jorong-kode px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
            </div>
            <button type="button" onclick="hapusBaris(this)"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
            list.appendChild(div);
            div.querySelector('.jorong-nama').focus();
        }

        function hapusBaris(btn) {
            btn.closest('.jorong-item').remove();
        }

        // ── Modal Tambah ────────────────────────────────────────────
        function bukaModalTambah() {
            resetModalTambah();
            loadProvinsi('tambah');
            document.getElementById('modal-tambah').classList.remove('hidden');
        }

        function bukaModalTambahDiWilayah(provinsi, kabupaten, kecamatan, desa, nagari) {
            resetModalTambah();
            document.getElementById('tambah-nagari').value = nagari !== 'null' ? nagari : '';
            loadProvinsi('tambah', provinsi).then(async () => {
                if (kabupaten && kabupaten !== 'null') {
                    await loadKabupaten('tambah', kabupaten);
                    if (kecamatan && kecamatan !== 'null') {
                        await loadKecamatan('tambah', kecamatan);
                        if (desa && desa !== 'null') await loadDesa('tambah', desa);
                    }
                }
            });
            document.getElementById('modal-tambah').classList.remove('hidden');
        }

        function resetModalTambah() {
            // Reset wilayah
            ['tambah-provinsi', 'tambah-kabupaten', 'tambah-kecamatan', 'tambah-desa'].forEach(id => {
                const el = document.getElementById(id);
                el.innerHTML = id === 'tambah-provinsi' ? '<option value="">— Pilih Provinsi —</option>' :
                    id === 'tambah-kabupaten' ? '<option value="">— Pilih Kabupaten —</option>' :
                        id === 'tambah-kecamatan' ? '<option value="">— Pilih Kecamatan —</option>' :
                            '<option value="">— Pilih Desa —</option>';
                if (id !== 'tambah-provinsi') el.disabled = true;
            });
            document.getElementById('tambah-nagari').value = '';

            // Reset list jorong ke 1 baris kosong
            document.getElementById('jorong-list').innerHTML = `
            <div class="jorong-item flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 border border-gray-200 dark:border-gray-700">
                <div class="flex-1 grid grid-cols-2 gap-2">
                    <input type="text" placeholder="Nama Jorong *"
                        class="jorong-nama px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
                    <input type="text" placeholder="Kode (otomatis)"
                        class="jorong-kode px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 w-full">
                </div>
                <div class="w-8 flex-shrink-0"></div>
            </div>`;
        }

        function tutupModalTambah() {
            document.getElementById('modal-tambah').classList.add('hidden');
        }

        async function simpanTambah() {
            const provinsi = document.getElementById('tambah-provinsi').value;
            const kabupaten = document.getElementById('tambah-kabupaten').value;
            const kecamatan = document.getElementById('tambah-kecamatan').value;
            const desa = document.getElementById('tambah-desa').value;
            const nagari = document.getElementById('tambah-nagari').value.trim();

            // Kumpulkan semua baris jorong
            const items = document.querySelectorAll('.jorong-item');
            const jorongData = [];
            let valid = true;

            items.forEach((item, i) => {
                const nama = item.querySelector('.jorong-nama').value.trim();
                const kode = item.querySelector('.jorong-kode').value.trim();
                if (!nama) {
                    item.querySelector('.jorong-nama').classList.add('border-red-400', 'ring-2', 'ring-red-200');
                    valid = false;
                } else {
                    item.querySelector('.jorong-nama').classList.remove('border-red-400', 'ring-2', 'ring-red-200');
                    jorongData.push({ nama_jorong: nama, kode_jorong: kode });
                }
            });

            if (!valid) { showAlert('danger', '❌ Nama jorong tidak boleh kosong!'); return; }
            if (jorongData.length === 0) { showAlert('danger', '❌ Tambahkan minimal 1 jorong!'); return; }

            const btn = document.getElementById('btnSimpan');
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menyimpan...';

            // Simpan satu per satu
            let berhasil = 0, gagal = 0;
            for (const j of jorongData) {
                try {
                    const res = await fetch('{{ route("admin.jorong.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ ...j, provinsi, kabupaten, kecamatan, desa, nagari }),
                    });
                    const data = await res.json();
                    if (data.success) berhasil++; else gagal++;
                } catch (e) { gagal++; }
            }

            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Semua';

            tutupModalTambah();

            if (gagal === 0) showAlert('success', `✅ ${berhasil} jorong berhasil ditambahkan!`);
            else showAlert('danger', `⚠️ ${berhasil} berhasil, ${gagal} gagal (nama mungkin sudah ada).`);

            setTimeout(() => location.reload(), 1500);
        }

        // ── Modal Edit ─────────────────────────────────────────────
        function editJorong(id, nama, kode, provinsi, kabupaten, kecamatan, desa, nagari) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-kode').value = kode === 'null' ? '' : kode;
            document.getElementById('edit-nagari').value = nagari === 'null' ? '' : nagari;
            // document.getElementById('edit-keterangan').value = keterangan === 'null' ? '' : keterangan;

            const prov = provinsi === 'null' ? '' : provinsi;
            const kab = kabupaten === 'null' ? '' : kabupaten;
            const kec = kecamatan === 'null' ? '' : kecamatan;
            const des = desa === 'null' ? '' : desa;

            loadProvinsi('edit', prov).then(async () => {
                if (prov && kab) {
                    await loadKabupaten('edit', kab);
                    if (kec) {
                        await loadKecamatan('edit', kec);
                        if (des) await loadDesa('edit', des);
                    }
                }
            });

            document.getElementById('modal-edit').classList.remove('hidden');
        }

        function simpanEdit() {
            const id = document.getElementById('edit-id').value;
            const nama = document.getElementById('edit-nama').value.trim();
            if (!nama) { showAlert('danger', '❌ Nama jorong wajib diisi!'); return; }

            fetch(`/admin/jorong/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    nama_jorong: nama,
                    kode_jorong: document.getElementById('edit-kode').value.trim(),
                    provinsi: document.getElementById('edit-provinsi').value,
                    kabupaten: document.getElementById('edit-kabupaten').value,
                    kecamatan: document.getElementById('edit-kecamatan').value,
                    desa: document.getElementById('edit-desa').value,
                    nagari: document.getElementById('edit-nagari').value.trim(),
                    // keterangan:  document.getElementById('edit-keterangan').value.trim(),
                }),
            })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modal-edit').classList.add('hidden');
                    if (data.success) { showAlert('success', '✅ ' + data.message); setTimeout(() => location.reload(), 1500); }
                    else showAlert('danger', '❌ ' + (data.message || 'Terjadi kesalahan.'));
                });
        }

        // ── Hapus & Toggle ─────────────────────────────────────────
        function hapusJorong(id) {
            if (!confirm('Yakin hapus jorong ini?')) return;
            fetch(`/admin/jorong/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { showAlert('success', '✅ ' + data.message); setTimeout(() => location.reload(), 1500); }
                    else showAlert('danger', '❌ ' + data.message);
                });
        }

        function toggleAktif(id, btn) {
            fetch(`/admin/jorong/${id}/toggle`, { method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        btn.textContent = data.aktif ? 'Aktif' : 'Nonaktif';
                        btn.className = 'px-2 py-1 rounded-full text-xs font-semibold transition-colors ' +
                            (data.aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500');
                    }
                });
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { tutupModalTambah(); document.getElementById('modal-edit').classList.add('hidden'); }
        });
    </script>
@endpush