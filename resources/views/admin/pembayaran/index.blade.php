@extends('layouts.app')

@section('title', 'Kasir Pembayaran')
@section('page_title', 'Kasir Pembayaran')
@section('page_subtitle', 'Proses pembayaran tagihan air — PAMSIMAS Nagari Bayua')

@section('content')

<div id="alert-box" class="hidden mb-4">
    <div id="alert-content" class="flex items-center gap-3 p-4 rounded-xl border text-sm font-medium">
        <span id="alert-message"></span>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Belum Bayar</p>
        <p class="text-2xl font-bold text-amber-500">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Lunas</p>
        <p class="text-2xl font-bold text-emerald-500">{{ $stats['konfirmasi'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Ditolak</p>
        <p class="text-2xl font-bold text-red-500">{{ $stats['ditolak'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Terkumpul</p>
        <p class="text-lg font-bold text-emerald-600">Rp {{ number_format($stats['total'], 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 mb-6">
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Pelanggan</label>
    <select id="select-pelanggan" onchange="pilihPelanggan(this.value)"
        class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
        <option value="">-- Pilih pelanggan --</option>
        @foreach($pelanggan as $p)
        <option value="{{ $p->id }}">{{ $p->nama_pelanggan }} ({{ $p->nomor_pelanggan }})</option>
        @endforeach
    </select>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div id="kalender-kosong" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-12 text-center text-gray-400">
            <p class="text-5xl mb-3">👤</p>
            <p class="text-sm font-medium">Pilih pelanggan di atas</p>
            <p class="text-xs mt-1">untuk melihat riwayat pembayaran</p>
        </div>

        <div id="kalender-panel" class="hidden bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white" id="kal-nama">-</p>
                    <p class="text-xs text-gray-400" id="kal-id">-</p>
                </div>
                <div class="flex gap-2" id="tahun-tabs"></div>
            </div>
            <div class="flex flex-wrap gap-3 px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-1.5 text-xs text-gray-500"><div class="w-3 h-3 rounded bg-emerald-200 border border-emerald-400"></div> Lunas</div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500"><div class="w-3 h-3 rounded bg-red-200 border border-red-400"></div> Nunggak</div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500"><div class="w-3 h-3 rounded bg-amber-100 border border-amber-400"></div> Belum daftar</div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500"><div class="w-3 h-3 rounded bg-gray-100 border border-gray-300"></div> Bukan periode</div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-6 gap-3" id="bulan-grid"></div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm sticky top-4">

            <div id="panel-kosong" class="px-6 py-12 text-center text-gray-400">
                <p class="text-5xl mb-3">🧮</p>
                <p class="text-sm font-medium">Klik bulan di kalender</p>
                <p class="text-xs mt-1">untuk melihat detail atau bayar</p>
            </div>

            <div id="panel-kasir" class="hidden">
                <div id="kasir-header" class="px-5 py-4 bg-emerald-500 rounded-t-2xl">
                    <p id="kasir-header-title" class="text-white font-semibold text-sm">🧾 Proses Pembayaran</p>
                    <p class="text-emerald-100 text-xs mt-0.5" id="kasir-nomor">-</p>
                </div>

                <div class="px-5 py-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 mb-4">
                        <p class="text-xs text-gray-400 mb-0.5">Pelanggan</p>
                        <p class="font-semibold text-gray-800 dark:text-white" id="kasir-nama">-</p>
                        <p class="text-xs text-gray-400 mt-1" id="kasir-periode">-</p>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tagihan Air</span>
                            <span class="text-gray-800 dark:text-white" id="kasir-tagihan">-</span>
                        </div>
                        <div class="flex justify-between text-sm" id="row-denda" style="display:none!important">
                            <span class="text-red-400">Denda</span>
                            <span class="text-red-500" id="kasir-denda">-</span>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-2 flex justify-between">
                            <span class="font-semibold text-gray-800 dark:text-white">Total Bayar</span>
                            <span class="font-bold text-emerald-600 text-lg" id="kasir-total">-</span>
                        </div>
                    </div>

                    {{-- Panel Lunas --}}
                    <div id="panel-lunas" class="hidden">
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 text-center">
                            <p class="text-3xl mb-2">✅</p>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Tagihan Sudah Lunas</p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Pembayaran telah dikonfirmasi</p>
                        </div>
                    </div>

                    {{-- Panel Metode --}}
                    <div id="panel-metode">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Metode Pembayaran</p>
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <button onclick="pilihMetode('tunai')" id="btn-metode-tunai"
                                class="metode-btn border-2 border-emerald-500 bg-emerald-50 text-emerald-700 rounded-xl py-3 text-xs font-semibold transition-all">
                                💵 Tunai
                            </button>
                            <button onclick="pilihMetode('qris')" id="btn-metode-qris"
                                class="metode-btn border-2 border-gray-200 dark:border-gray-700 text-gray-500 rounded-xl py-3 text-xs font-semibold transition-all">
                                📱 QRIS
                            </button>
                            <button onclick="pilihMetode('transfer')" id="btn-metode-transfer"
                                class="metode-btn border-2 border-gray-200 dark:border-gray-700 text-gray-500 rounded-xl py-3 text-xs font-semibold transition-all">
                                🏦 Transfer Bank
                            </button>
                            <button onclick="pilihMetode('ewallet')" id="btn-metode-ewallet"
                                class="metode-btn border-2 border-gray-200 dark:border-gray-700 text-gray-500 rounded-xl py-3 text-xs font-semibold transition-all">
                                👛 E-Wallet
                            </button>
                        </div>

                        <div id="panel-tunai">
                            <div class="mb-3">
                                <label class="text-xs text-gray-400 mb-1 block">Uang Diterima (Rp)</label>
                                <input type="number" id="uang-diterima"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400"
                                    placeholder="Contoh: 50000" oninput="hitungKembalian()">
                            </div>
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl px-4 py-3 flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-500">Kembalian</span>
                                <span class="font-bold text-emerald-600" id="kembalian-text">Rp 0</span>
                            </div>
                            <button onclick="prosesTunai()"
                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                                ✅ Konfirmasi Pembayaran Tunai
                            </button>
                        </div>

                        <div id="panel-online" class="hidden">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 mb-4 text-xs text-blue-700 dark:text-blue-300">
                                Pelanggan akan diarahkan ke halaman pembayaran Midtrans. Mendukung QRIS, Transfer Bank, GoPay, OVO, Dana, ShopeePay.
                            </div>
                            <button onclick="prosesMidtrans()"
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                                🔗 Buka Halaman Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
const bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const tahunSekarang = {{ now()->year }};
const bulanSekarang = {{ now()->month }};
const urlTagihanPelanggan = '{{ url("admin/pembayaran/pelanggan") }}';

let selectedId    = null;
let selectedTotal = 0;
let allTagihan    = [];
let tahunAktif    = tahunSekarang;
let namaPelanggan = '';

function formatRp(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function showAlert(type, message) {
    const box = document.getElementById('alert-box');
    const content = document.getElementById('alert-content');
    const styles = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        danger:  'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-amber-50 border-amber-200 text-amber-800',
    };
    content.className = `flex items-center gap-3 p-4 rounded-xl border text-sm font-medium ${styles[type]}`;
    document.getElementById('alert-message').textContent = message;
    box.classList.remove('hidden');
    setTimeout(() => box.classList.add('hidden'), 5000);
}

function pilihPelanggan(pelangganId) {
    if (!pelangganId) {
        document.getElementById('kalender-kosong').classList.remove('hidden');
        document.getElementById('kalender-panel').classList.add('hidden');
        resetKasir();
        return;
    }
    fetch(urlTagihanPelanggan + '/' + pelangganId + '/tagihan')
        .then(res => res.json())
        .then(data => {
            allTagihan    = data.tagihan;
            namaPelanggan = data.pelanggan.nama_pelanggan;
            tahunAktif    = tahunSekarang;

            document.getElementById('kal-nama').textContent = data.pelanggan.nama_pelanggan;
            document.getElementById('kal-id').textContent   = data.pelanggan.nomor_pelanggan;

            const tahunList = [...new Set(data.tagihan.map(t => t.tahun))].sort((a,b) => b-a);
            if (!tahunList.includes(tahunSekarang)) tahunList.unshift(tahunSekarang);

            const tabs = document.getElementById('tahun-tabs');
            tabs.innerHTML = '';
            tahunList.forEach(t => {
                const btn = document.createElement('button');
                btn.textContent = t;
                btn.className = 'px-3 py-1 rounded-full text-xs font-medium border transition-colors ' +
                    (t === tahunAktif ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border-gray-200');
                btn.onclick = () => {
                    tahunAktif = t;
                    tabs.querySelectorAll('button').forEach(b => {
                        b.className = 'px-3 py-1 rounded-full text-xs font-medium border transition-colors bg-gray-100 dark:bg-gray-800 text-gray-500 border-gray-200';
                    });
                    btn.className = 'px-3 py-1 rounded-full text-xs font-medium border transition-colors bg-emerald-500 text-white border-emerald-500';
                    renderKalender(t);
                };
                tabs.appendChild(btn);
            });

            document.getElementById('kalender-kosong').classList.add('hidden');
            document.getElementById('kalender-panel').classList.remove('hidden');
            renderKalender(tahunAktif);
            resetKasir();
        });
}

function renderKalender(tahun) {
    const grid = document.getElementById('bulan-grid');
    grid.innerHTML = '';

    for (let i = 0; i < 12; i++) {
        const bulan = i + 1;
        const t = allTagihan.find(t => t.bulan === bulan && t.tahun === tahun);
        const isFuture = tahun === tahunSekarang && bulan > bulanSekarang;

        let bg, border, text, icon, clickable = false, cursor = 'cursor-default';

        if (isFuture) {
            bg = 'bg-gray-100 dark:bg-gray-800'; border = 'border-gray-200 dark:border-gray-700';
            text = 'text-gray-300'; icon = '—';
        } else if (!t) {
            bg = 'bg-amber-50 dark:bg-amber-900/20'; border = 'border-amber-200';
            text = 'text-amber-600'; icon = '?';
        } else if (t.status === 'lunas') {
            bg = 'bg-emerald-100 dark:bg-emerald-900/30'; border = 'border-emerald-400';
            text = 'text-emerald-700'; icon = '✓';
            clickable = true; cursor = 'cursor-pointer hover:scale-105';
        } else {
            bg = 'bg-red-100 dark:bg-red-900/30'; border = 'border-red-400';
            text = 'text-red-700'; icon = '!';
            clickable = true; cursor = 'cursor-pointer hover:scale-105';
        }

        const div = document.createElement('div');
        div.className = `${bg} border ${border} ${text} ${cursor} rounded-xl p-3 text-center transition-transform select-none`;
        div.innerHTML = `<div class="text-xs font-medium mb-1">${bulanNama[i]}</div><div class="text-lg font-bold">${icon}</div>`;

        if (clickable && t) {
            if (t.status === 'lunas') {
                div.onclick = () => lihatLunas(t);
                div.title   = 'Sudah lunas — klik untuk lihat detail';
            } else {
                div.onclick = () => bukaPembayaran(t);
                div.title   = 'Belum bayar — klik untuk proses pembayaran';
            }
        }

        grid.appendChild(div);
    }
}

function lihatLunas(t) {
    selectedId    = null;
    selectedTotal = 0;

    document.getElementById('kasir-nomor').textContent   = t.nomor_tagihan;
    document.getElementById('kasir-nama').textContent    = namaPelanggan;
    document.getElementById('kasir-periode').textContent = 'Periode: ' + bulanNama[t.bulan - 1] + ' ' + t.tahun;
    document.getElementById('kasir-tagihan').textContent = formatRp(t.total_tagihan);
    document.getElementById('kasir-total').textContent   = formatRp(t.total_bayar);
    document.getElementById('row-denda').style.display   = 'none';

    // Header biru untuk lunas
    document.getElementById('kasir-header').className       = 'px-5 py-4 bg-blue-500 rounded-t-2xl';
    document.getElementById('kasir-header-title').textContent = '✅ Sudah Lunas';

    document.getElementById('panel-lunas').classList.remove('hidden');
    document.getElementById('panel-metode').classList.add('hidden');
    document.getElementById('panel-kosong').classList.add('hidden');
    document.getElementById('panel-kasir').classList.remove('hidden');
}

function bukaPembayaran(t) {
    selectedId    = t.id;
    selectedTotal = parseFloat(t.total_bayar);

    document.getElementById('kasir-nomor').textContent   = t.nomor_tagihan;
    document.getElementById('kasir-nama').textContent    = namaPelanggan;
    document.getElementById('kasir-periode').textContent = 'Periode: ' + bulanNama[t.bulan - 1] + ' ' + t.tahun;
    document.getElementById('kasir-tagihan').textContent = formatRp(t.total_tagihan);
    document.getElementById('kasir-total').textContent   = formatRp(t.total_bayar);

    if (t.denda > 0) {
        document.getElementById('kasir-denda').textContent = formatRp(t.denda);
        document.getElementById('row-denda').style.display = 'flex';
    } else {
        document.getElementById('row-denda').style.display = 'none';
    }

    // Header hijau untuk bayar
    document.getElementById('kasir-header').className       = 'px-5 py-4 bg-emerald-500 rounded-t-2xl';
    document.getElementById('kasir-header-title').textContent = '🧾 Proses Pembayaran';

    document.getElementById('panel-lunas').classList.add('hidden');
    document.getElementById('panel-metode').classList.remove('hidden');
    document.getElementById('panel-kosong').classList.add('hidden');
    document.getElementById('panel-kasir').classList.remove('hidden');

    document.getElementById('uang-diterima').value = '';
    document.getElementById('kembalian-text').textContent = 'Rp 0';
    pilihMetode('tunai');
}

function resetKasir() {
    selectedId = null;
    selectedTotal = 0;
    document.getElementById('panel-kosong').classList.remove('hidden');
    document.getElementById('panel-kasir').classList.add('hidden');
    document.getElementById('panel-lunas').classList.add('hidden');
    document.getElementById('panel-metode').classList.remove('hidden');
}

function pilihMetode(metode) {
    document.getElementById('panel-lunas').classList.add('hidden');
    document.querySelectorAll('.metode-btn').forEach(b => {
        b.classList.remove('border-emerald-500','bg-emerald-50','text-emerald-700','border-blue-500','bg-blue-50','text-blue-700');
        b.classList.add('border-gray-200','dark:border-gray-700','text-gray-500');
    });
    const btn = document.getElementById('btn-metode-' + metode);
    if (metode === 'tunai') {
        btn.classList.add('border-emerald-500','bg-emerald-50','text-emerald-700');
        document.getElementById('panel-tunai').classList.remove('hidden');
        document.getElementById('panel-online').classList.add('hidden');
    } else {
        btn.classList.add('border-blue-500','bg-blue-50','text-blue-700');
        document.getElementById('panel-tunai').classList.add('hidden');
        document.getElementById('panel-online').classList.remove('hidden');
    }
}

function hitungKembalian() {
    const diterima  = parseInt(document.getElementById('uang-diterima').value) || 0;
    const kembalian = diterima - selectedTotal;
    const el = document.getElementById('kembalian-text');
    el.textContent = kembalian >= 0 ? formatRp(kembalian) : '⚠ Kurang ' + formatRp(Math.abs(kembalian));
    el.className = kembalian >= 0 ? 'font-bold text-emerald-600' : 'font-bold text-red-500';
}

function prosesTunai() {
    if (!selectedId) return;
    const diterima = parseInt(document.getElementById('uang-diterima').value) || 0;
    if (diterima < selectedTotal) { showAlert('warning', '⚠️ Uang diterima kurang!'); return; }

    fetch('{{ route("admin.pembayaran.tunai") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ tagihan_id: selectedId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert('success', '✅ ' + data.message);
            setTimeout(() => pilihPelanggan(document.getElementById('select-pelanggan').value), 1500);
        } else {
            showAlert('danger', '❌ ' + (data.message || 'Terjadi kesalahan.'));
        }
    })
    .catch(() => showAlert('danger', '❌ Terjadi kesalahan.'));
}

function prosesMidtrans() {
    if (!selectedId) return;
    fetch('{{ route("admin.pembayaran.midtrans") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ tagihan_id: selectedId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            snap.pay(data.snap_token, {
                onSuccess: () => { showAlert('success', '✅ Pembayaran berhasil!'); setTimeout(() => pilihPelanggan(document.getElementById('select-pelanggan').value), 2000); },
                onPending: () => showAlert('warning', '⏳ Pembayaran pending.'),
                onError:   () => showAlert('danger', '❌ Pembayaran gagal.'),
                onClose:   () => showAlert('warning', '⚠️ Popup ditutup.'),
            });
        } else {
            showAlert('danger', '❌ ' + (data.message || 'Gagal.'));
        }
    })
    .catch(() => showAlert('danger', '❌ Terjadi kesalahan.'));
}
</script>
@endpush