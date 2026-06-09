{{-- resources/views/admin/pembayaran/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pembayaran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola pembayaran tagihan pelanggan</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $stats['pending'] }}</div>
            <div class="text-sm text-amber-600 dark:text-amber-400 mt-1">Belum Bayar</div>
        </div>
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $stats['konfirmasi'] }}</div>
            <div class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Terkonfirmasi</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $stats['ditolak'] }}</div>
            <div class="text-sm text-red-600 dark:text-red-400 mt-1">Ditolak</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                Rp {{ number_format($stats['total'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">Total Terkumpul</div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-4 text-emerald-800 dark:text-emerald-300">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4 text-red-800 dark:text-red-300">
        {{ session('error') }}
    </div>
    @endif

    {{-- Form Pembayaran --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Input Pembayaran Baru</h2>

        {{-- Step 1: Pilih Pelanggan --}}
        <div id="step-pelanggan" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                1. Pilih Pelanggan
            </label>
            <select id="select-pelanggan"
                    onchange="pilihPelanggan(this.value)"
                    class="w-full md:w-96 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($pelanggan as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_pelanggan }} ({{ $p->nomor_pelanggan }})</option>
                @endforeach
            </select>
        </div>

        {{-- Step 2: Pilih Bulan Tagihan --}}
        <div id="step-tagihan" class="mb-6 hidden">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                2. Pilih Tagihan
            </label>
            <div id="loading-tagihan" class="hidden text-sm text-gray-500">Memuat tagihan...</div>
            <div id="list-tagihan" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
        </div>

        {{-- Step 3: Pilih Metode Bayar --}}
        <div id="step-metode" class="mb-6 hidden">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                3. Pilih Metode Pembayaran
            </label>
            <div class="flex flex-wrap gap-3">
                <button type="button"
                        onclick="pilihMetode('tunai')"
                        id="btn-tunai"
                        class="px-5 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:border-green-500 hover:text-green-600 transition-all">
                    💵 Tunai
                </button>
                <button type="button"
                        onclick="pilihMetode('pakasir')"
                        id="btn-pakasir"
                        class="px-5 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:border-blue-500 hover:text-blue-600 transition-all">
                    📲 Pakasir (QRIS / Transfer / E-Wallet)
                </button>
            </div>
        </div>

        {{-- Panel Tunai --}}
        <div id="panel-tunai" class="hidden">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-5">
                <h3 class="font-semibold text-green-800 dark:text-green-300 mb-3">💵 Pembayaran Tunai</h3>
                <div id="info-tagihan-tunai" class="text-sm text-gray-600 dark:text-gray-400 mb-4"></div>
                <button type="button"
                        onclick="prosesTunai()"
                        id="btn-proses-tunai"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors disabled:opacity-50">
                    ✅ Konfirmasi Pembayaran Tunai
                </button>
            </div>
        </div>

        {{-- Panel Pakasir (Online) --}}
        <div id="panel-pakasir" class="hidden">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
                <h3 class="font-semibold text-blue-800 dark:text-blue-300 mb-1">
                    📲 Pakasir — QRIS, Transfer Bank, semua metode tersedia
                </h3>
                <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">
                    Sistem akan membuat link pembayaran. Arahkan pelanggan ke link tersebut untuk menyelesaikan pembayaran.
                </p>
                <div id="info-tagihan-pakasir" class="text-sm text-gray-600 dark:text-gray-400 mb-4"></div>

                {{-- Tombol buat link --}}
                <button type="button"
                        onclick="prosesPakasir()"
                        id="btn-proses-pakasir"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors disabled:opacity-50">
                    🔗 Buka Halaman Pembayaran Pakasir
                </button>

                {{-- Area setelah link dibuat --}}
                <div id="area-link-pakasir" class="hidden mt-5 space-y-4">
                    <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ✅ Link pembayaran sudah dibuka di tab baru. Salin link jika dibutuhkan:
                        </p>
                        <div class="flex items-center gap-2">
                            <input id="input-payment-url"
                                   type="text"
                                   readonly
                                   class="flex-1 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-600 dark:text-gray-400 truncate" />
                            <button type="button"
                                    onclick="salinLink()"
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                                📋 Salin
                            </button>
                        </div>
                    </div>

                    {{-- Polling status --}}
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-amber-800 dark:text-amber-300">Status Pembayaran</span>
                            <span id="badge-status-pakasir"
                                  class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">
                                Menunggu...
                            </span>
                        </div>
                        <p id="teks-status-pakasir" class="text-sm text-amber-700 dark:text-amber-400">
                            Menunggu pelanggan menyelesaikan pembayaran...
                        </p>
                        <div class="mt-3 flex gap-2">
                            <button type="button"
                                    onclick="cekStatusPakasir()"
                                    id="btn-cek-status"
                                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors">
                                🔄 Cek Status Sekarang
                            </button>
                            <button type="button"
                                    onclick="toggleAutoRefresh()"
                                    id="btn-auto-refresh"
                                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                ⏸ Hentikan Auto-Refresh
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2" id="teks-auto-refresh">
                            Status diperbarui otomatis setiap 5 detik.
                        </p>
                    </div>

                    {{-- Sukses banner --}}
                    <div id="banner-sukses-pakasir" class="hidden bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-300 dark:border-emerald-700 rounded-xl p-4">
                        <p class="text-emerald-800 dark:text-emerald-300 font-semibold">
                            ✅ Pembayaran berhasil dikonfirmasi! Tagihan telah diperbarui menjadi Lunas.
                        </p>
                        <button type="button"
                                onclick="window.location.reload()"
                                class="mt-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Refresh Halaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Tagihan Belum Bayar --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tagihan Belum Lunas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Tagihan</th>
                        <th class="px-6 py-3">Periode</th>
                        <th class="px-6 py-3">Total Bayar</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tagihan as $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $t->pelanggan->nama_pelanggan }}</div>
                            <div class="text-xs text-gray-500">{{ $t->pelanggan->nomor_pelanggan }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-mono text-xs">
                            {{ $t->nomor_tagihan }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            {{ $t->periodeTeks() }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($t->total_bayar ?: $t->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">
                                {{ $t->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.pembayaran.show', $t->pembayaran ?? '#') }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline text-xs mr-3">
                                Detail
                            </a>
                            <button type="button"
                                    onclick="konfirmasiCepat({{ $t->id }}, '{{ $t->nomor_tagihan }}')"
                                    class="text-emerald-600 dark:text-emerald-400 hover:underline text-xs">
                                Konfirmasi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                            Semua tagihan sudah lunas 🎉
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal Konfirmasi Cepat --}}
<div id="modal-konfirmasi" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Konfirmasi Pembayaran</h3>
        <p id="modal-konfirmasi-text" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>
        <form id="form-konfirmasi" method="POST">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (opsional)</label>
                <textarea name="catatan"
                          rows="2"
                          class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="document.getElementById('modal-konfirmasi').classList.add('hidden'); document.getElementById('modal-konfirmasi').classList.remove('flex');"
                        class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm transition-colors">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── State ────────────────────────────────────────────────────
let selectedPelangganId = null;
let selectedTagihanId   = null;
let selectedNomorTagihan = null;
let selectedMerchantRef  = null;
let pollingInterval      = null;
let autoRefreshAktif     = false;

// ── Step 1: Pilih Pelanggan ──────────────────────────────────
function pilihPelanggan(pelangganId) {
    if (!pelangganId) return;

    selectedPelangganId = pelangganId;
    selectedTagihanId   = null;
    selectedMerchantRef = null;

    document.getElementById('step-tagihan').classList.remove('hidden');
    document.getElementById('step-metode').classList.add('hidden');
    document.getElementById('panel-tunai').classList.add('hidden');
    document.getElementById('panel-pakasir').classList.add('hidden');
    document.getElementById('list-tagihan').innerHTML = '';
    document.getElementById('loading-tagihan').classList.remove('hidden');

    fetch(`/admin/pembayaran/pelanggan/${pelangganId}/tagihan`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('loading-tagihan').classList.add('hidden');
            renderTagihan(data.tagihan);
        })
        .catch(() => {
            document.getElementById('loading-tagihan').classList.add('hidden');
            document.getElementById('list-tagihan').innerHTML =
                '<p class="text-red-500 text-sm">Gagal memuat tagihan.</p>';
        });
}

function renderTagihan(tagihanList) {
    const container = document.getElementById('list-tagihan');

    if (!tagihanList || tagihanList.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-sm">Tidak ada tagihan tersedia.</p>';
        return;
    }

    const bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    container.innerHTML = '';

    tagihanList.forEach(t => {
        const lunas   = t.status === 'lunas';
        const label   = `${bulanNama[t.bulan]} ${t.tahun}`;
        const nominal = 'Rp ' + parseInt(t.total_bayar || t.total_tagihan).toLocaleString('id-ID');

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.disabled = lunas;
        btn.className = lunas
            ? 'p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-400 cursor-not-allowed text-left'
            : 'p-3 rounded-xl border-2 border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 hover:border-amber-400 text-left cursor-pointer transition-all';
        btn.innerHTML = `
            <div class="font-semibold text-sm ${lunas ? 'text-gray-400' : 'text-gray-800 dark:text-white'}">${label}</div>
            <div class="text-xs mt-1 ${lunas ? 'text-gray-400' : 'text-gray-500 dark:text-gray-400'}">${nominal}</div>
            <div class="mt-1">
                <span class="text-xs px-2 py-0.5 rounded-full ${lunas ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}">
                    ${lunas ? 'Lunas' : 'Belum Bayar'}
                </span>
            </div>`;

        if (!lunas) {
            btn.onclick = () => pilihTagihan(t.id, t.nomor_tagihan, t.total_bayar || t.total_tagihan, label, btn);
        }

        container.appendChild(btn);
    });
}

// ── Step 2: Pilih Tagihan ────────────────────────────────────
function pilihTagihan(id, nomor, total, label, btnEl) {
    // Reset highlight
    document.querySelectorAll('#list-tagihan button').forEach(b => {
        b.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
    });
    btnEl.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');

    selectedTagihanId    = id;
    selectedNomorTagihan = nomor;
    selectedMerchantRef  = nomor;

    const info = `<strong>${label}</strong> — Rp ${parseInt(total).toLocaleString('id-ID')}`;
    document.getElementById('info-tagihan-tunai').innerHTML    = `Tagihan: ${info}`;
    document.getElementById('info-tagihan-pakasir').innerHTML  = `Tagihan: ${info}`;

    document.getElementById('step-metode').classList.remove('hidden');
    document.getElementById('panel-tunai').classList.add('hidden');
    document.getElementById('panel-pakasir').classList.add('hidden');
    resetAreaPakasir();
}

// ── Step 3: Pilih Metode ──────────────────────────────────────
function pilihMetode(metode) {
    document.getElementById('panel-tunai').classList.add('hidden');
    document.getElementById('panel-pakasir').classList.add('hidden');

    document.getElementById('btn-tunai').classList.remove('border-green-500', 'text-green-600');
    document.getElementById('btn-pakasir').classList.remove('border-blue-500', 'text-blue-600');

    if (metode === 'tunai') {
        document.getElementById('panel-tunai').classList.remove('hidden');
        document.getElementById('btn-tunai').classList.add('border-green-500', 'text-green-600');
    } else {
        document.getElementById('panel-pakasir').classList.remove('hidden');
        document.getElementById('btn-pakasir').classList.add('border-blue-500', 'text-blue-600');
    }
}

// ── Proses Tunai ──────────────────────────────────────────────
function prosesTunai() {
    if (!selectedTagihanId) return alert('Pilih tagihan terlebih dahulu.');

    const btn = document.getElementById('btn-proses-tunai');
    btn.disabled    = true;
    btn.textContent = 'Memproses...';

    fetch('{{ route('admin.pembayaran.tunai') }}', {
        method : 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN'  : '{{ csrf_token() }}',
            'Accept'        : 'application/json',
        },
        body: JSON.stringify({ tagihan_id: selectedTagihanId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Gagal memproses pembayaran.');
            btn.disabled    = false;
            btn.textContent = '✅ Konfirmasi Pembayaran Tunai';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled    = false;
        btn.textContent = '✅ Konfirmasi Pembayaran Tunai';
    });
}

// ── Proses Pakasir ────────────────────────────────────────────
function prosesPakasir() {
    if (!selectedTagihanId) return alert('Pilih tagihan terlebih dahulu.');

    const btn = document.getElementById('btn-proses-pakasir');
    btn.disabled    = true;
    btn.textContent = 'Membuat link...';

    fetch('{{ route('admin.pembayaran.pakasir') }}', {
        method : 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN'  : '{{ csrf_token() }}',
            'Accept'        : 'application/json',
        },
        body: JSON.stringify({ tagihan_id: selectedTagihanId }),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled    = false;
        btn.textContent = '🔗 Buat Ulang Link';

        if (data.success && data.payment_url) {
            selectedMerchantRef = data.merchant_ref || selectedNomorTagihan;

            // Buka di tab baru
            window.open(data.payment_url, '_blank');

            // Tampilkan area link & polling
            document.getElementById('input-payment-url').value = data.payment_url;
            document.getElementById('area-link-pakasir').classList.remove('hidden');

            // Mulai auto-refresh
            mulaiAutoRefresh();
        } else {
            alert(data.message || 'Gagal membuat link pembayaran.');
        }
    })
    .catch(() => {
        btn.disabled    = false;
        btn.textContent = '🔗 Buka Halaman Pembayaran Pakasir';
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
}

// ── Cek Status Pakasir ─────────────────────────────────────────
function cekStatusPakasir() {
    if (!selectedMerchantRef) return;

    const btn   = document.getElementById('btn-cek-status');
    btn.textContent = '🔄 Mengecek...';
    btn.disabled    = true;

    const url = `{{ url('admin/pakasir/status') }}/${encodeURIComponent(selectedMerchantRef)}`;

    fetch(url, {
        headers: {
            'Accept'       : 'application/json',
            'X-CSRF-TOKEN' : '{{ csrf_token() }}',
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled    = false;
        btn.textContent = '🔄 Cek Status Sekarang';
        updateBadgeStatus(data);
    })
    .catch(() => {
        btn.disabled    = false;
        btn.textContent = '🔄 Cek Status Sekarang';
    });
}

function updateBadgeStatus(data) {
    const badge = document.getElementById('badge-status-pakasir');
    const teks  = document.getElementById('teks-status-pakasir');
    const banner = document.getElementById('banner-sukses-pakasir');

    if (!data.found) {
        badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700';
        badge.textContent = 'Tidak ditemukan';
        teks.textContent  = 'Data transaksi tidak ditemukan.';
        return;
    }

    const statusMap = {
        pending : { cls: 'bg-amber-100 text-amber-800',   label: 'Menunggu',  teks: 'Menunggu pelanggan menyelesaikan pembayaran...' },
        paid    : { cls: 'bg-emerald-100 text-emerald-800',label: 'Lunas',     teks: 'Pembayaran telah diterima dan dikonfirmasi! ✅' },
        failed  : { cls: 'bg-red-100 text-red-800',        label: 'Gagal',     teks: 'Pembayaran gagal atau dibatalkan.' },
        expired : { cls: 'bg-gray-100 text-gray-800',      label: 'Kadaluarsa',teks: 'Link pembayaran telah kadaluarsa. Buat ulang jika diperlukan.' },
    };

    const s = statusMap[data.status] || statusMap['pending'];
    badge.className   = 'px-2.5 py-0.5 rounded-full text-xs font-semibold ' + s.cls;
    badge.textContent = s.label;
    teks.textContent  = s.teks;

    if (data.is_paid || data.tagihan_lunas) {
        banner.classList.remove('hidden');
        hentikanAutoRefresh();
    }
}

// ── Auto Refresh ──────────────────────────────────────────────
function mulaiAutoRefresh() {
    autoRefreshAktif = true;
    document.getElementById('btn-auto-refresh').textContent = '⏸ Hentikan Auto-Refresh';
    document.getElementById('teks-auto-refresh').textContent = 'Status diperbarui otomatis setiap 5 detik.';

    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        if (autoRefreshAktif) cekStatusPakasir();
    }, 5000);
}

function hentikanAutoRefresh() {
    autoRefreshAktif = false;
    if (pollingInterval) clearInterval(pollingInterval);
    document.getElementById('btn-auto-refresh').textContent = '▶ Mulai Auto-Refresh';
    document.getElementById('teks-auto-refresh').textContent = 'Auto-refresh dihentikan.';
}

function toggleAutoRefresh() {
    if (autoRefreshAktif) {
        hentikanAutoRefresh();
    } else {
        mulaiAutoRefresh();
    }
}

function resetAreaPakasir() {
    document.getElementById('area-link-pakasir').classList.add('hidden');
    document.getElementById('banner-sukses-pakasir').classList.add('hidden');
    document.getElementById('badge-status-pakasir').textContent = 'Menunggu...';
    document.getElementById('teks-status-pakasir').textContent  = 'Menunggu pelanggan menyelesaikan pembayaran...';
    hentikanAutoRefresh();
}

// ── Salin Link ─────────────────────────────────────────────────
function salinLink() {
    const input = document.getElementById('input-payment-url');
    input.select();
    document.execCommand('copy');
    alert('Link berhasil disalin!');
}

// ── Konfirmasi Cepat ───────────────────────────────────────────
function konfirmasiCepat(tagihanId, nomorTagihan) {
    const modal = document.getElementById('modal-konfirmasi');
    document.getElementById('modal-konfirmasi-text').textContent =
        `Konfirmasi pembayaran untuk tagihan ${nomorTagihan}?`;

    const form = document.getElementById('form-konfirmasi');
    form.action = `{{ url('admin/pembayaran') }}/${tagihanId}/konfirmasi`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
</script>
@endpush