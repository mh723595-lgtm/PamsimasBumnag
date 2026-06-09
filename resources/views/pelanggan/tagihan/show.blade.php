{{-- resources/views/pelanggan/tagihan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Tagihan ' . $tagihan->nomor_tagihan)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('pelanggan.tagihan.index') }}"
           class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Detail Tagihan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $tagihan->nomor_tagihan }}</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-4 flex items-start gap-3">
        <span class="text-emerald-500 mt-0.5 text-lg">✅</span>
        <p class="text-emerald-800 dark:text-emerald-300 text-sm">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4 flex items-start gap-3">
        <span class="text-red-500 mt-0.5 text-lg">❌</span>
        <p class="text-red-800 dark:text-red-300 text-sm">{{ session('error') }}</p>
    </div>
    @endif
    @if(session('info'))
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 flex items-start gap-3">
        <span class="text-blue-500 mt-0.5 text-lg">ℹ️</span>
        <p class="text-blue-800 dark:text-blue-300 text-sm">{{ session('info') }}</p>
    </div>
    @endif

    {{-- Status Badge --}}
    <div class="flex items-center gap-3">
        <span class="px-4 py-1.5 rounded-full text-sm font-semibold {{ $tagihan->statusBadge() }}">
            {{ $tagihan->statusLabel() }}
        </span>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            Periode: <strong class="text-gray-700 dark:text-gray-300">{{ $tagihan->periodeTeks() }}</strong>
        </span>
    </div>

    {{-- Info Tagihan --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Rincian Tagihan</h2>
        </div>
        <div class="px-6 py-4 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Nomor Tagihan</span>
                <span class="font-mono text-gray-800 dark:text-gray-200">{{ $tagihan->nomor_tagihan }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Periode</span>
                <span class="text-gray-800 dark:text-gray-200">{{ $tagihan->periodeTeks() }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Pemakaian Air</span>
                <span class="text-gray-800 dark:text-gray-200">{{ number_format($tagihan->pemakaian, 2) }} m³</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Tanggal Tagihan</span>
                <span class="text-gray-800 dark:text-gray-200">
                    {{ $tagihan->tanggal_tagihan?->format('d M Y') ?? '-' }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Jatuh Tempo</span>
                <span class="text-gray-800 dark:text-gray-200 {{ $tagihan->isTerlambat() ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                    {{ $tagihan->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}
                    @if($tagihan->isTerlambat())
                        <span class="text-xs">(Terlambat)</span>
                    @endif
                </span>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Biaya Air</span>
                    <span class="text-gray-800 dark:text-gray-200">
                        Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                    </span>
                </div>
                @if($tagihan->hasDenda())
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-red-500 dark:text-red-400">Denda Keterlambatan</span>
                    <span class="text-red-600 dark:text-red-400">
                        + Rp {{ number_format($tagihan->denda, 0, ',', '.') }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-base mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-gray-900 dark:text-white">Total yang Harus Dibayar</span>
                    <span class="text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($tagihan->total_bayar ?: $tagihan->total_tagihan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Pembayaran --}}
    @if($tagihan->isLunas())
        {{-- Sudah Lunas --}}
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center text-xl">
                    ✅
                </div>
                <div>
                    <h3 class="font-semibold text-emerald-800 dark:text-emerald-300">Tagihan Sudah Lunas</h3>
                    <p class="text-sm text-emerald-600 dark:text-emerald-400">Terima kasih telah membayar tepat waktu.</p>
                </div>
            </div>
            @if($tagihan->pembayaran)
            <div class="mt-3 pt-3 border-t border-emerald-200 dark:border-emerald-700 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-emerald-700 dark:text-emerald-400">No. Pembayaran</span>
                    <span class="font-mono text-emerald-800 dark:text-emerald-300">{{ $tagihan->pembayaran->nomor_pembayaran }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-700 dark:text-emerald-400">Tanggal Bayar</span>
                    <span class="text-emerald-800 dark:text-emerald-300">
                        {{ $tagihan->pembayaran->tanggal_bayar?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-700 dark:text-emerald-400">Metode</span>
                    <span class="capitalize text-emerald-800 dark:text-emerald-300">{{ $tagihan->pembayaran->metode_bayar }}</span>
                </div>
                <div class="flex justify-between font-semibold">
                    <span class="text-emerald-700 dark:text-emerald-400">Jumlah Dibayar</span>
                    <span class="text-emerald-800 dark:text-emerald-300">
                        Rp {{ number_format($tagihan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endif
        </div>

    @else
        {{-- Belum Lunas: Tampilkan tombol Pakasir --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-100 dark:border-blue-800 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center text-xl">
                    💳
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-200">Tagihan Belum Dibayar</h3>
                    <p class="text-sm text-blue-600 dark:text-blue-400">
                        Bayar sekarang dengan mudah melalui Pakasir
                    </p>
                </div>
            </div>

            {{-- Total yang harus dibayar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-4 flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total yang harus dibayar</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($tagihan->total_bayar ?: $tagihan->total_tagihan, 0, ',', '.') }}
                </span>
            </div>

            {{-- Jatuh tempo warning --}}
            @if($tagihan->tanggal_jatuh_tempo && $tagihan->tanggal_jatuh_tempo->isFuture())
            <div class="text-xs text-amber-600 dark:text-amber-400 mb-4 flex items-center gap-1.5">
                <span>⚠️</span>
                <span>Jatuh tempo: <strong>{{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}</strong>
                    ({{ $tagihan->tanggal_jatuh_tempo->diffForHumans() }})</span>
            </div>
            @elseif($tagihan->isTerlambat())
            <div class="text-xs text-red-600 dark:text-red-400 mb-4 flex items-center gap-1.5">
                <span>🚨</span>
                <span>Tagihan ini sudah melewati jatuh tempo. Denda telah diterapkan.</span>
            </div>
            @endif

            @php
                // Cek apakah ada transaksi Pakasir yang masih aktif
                $activeTx = $tagihan->pakasirTransactions()
                    ->where('status', 'pending')
                    ->where(function($q) {
                        $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
                    })
                    ->latest()
                    ->first() ?? null;
            @endphp

            @if($activeTx && $activeTx->payment_url)
                {{-- Sudah ada link aktif --}}
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-3 mb-3">
                    <p class="text-xs text-amber-700 dark:text-amber-300 mb-2">
                        ℹ️ Link pembayaran aktif sudah tersedia. Gunakan link di bawah untuk menyelesaikan pembayaran.
                    </p>
                    <a href="{{ $activeTx->payment_url }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-colors">
                        🔗 Lanjutkan Pembayaran
                    </a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 text-center">— atau buat link baru —</p>
            @endif

            {{-- Tombol utama bayar via Pakasir --}}
            <form method="POST" action="{{ route('pelanggan.pakasir.bayar', $tagihan) }}" id="form-bayar-pakasir">
                @csrf
                <button type="submit"
                        id="btn-bayar-pakasir"
                        onclick="setBtnLoading()"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold rounded-xl text-base transition-colors shadow-sm shadow-blue-200 dark:shadow-none flex items-center justify-center gap-2">
                    <span id="btn-bayar-icon">💳</span>
                    <span id="btn-bayar-text">Bayar Sekarang via Pakasir</span>
                </button>
            </form>

            {{-- Info metode --}}
            <div class="mt-3 flex items-center justify-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                <span class="flex items-center gap-1">📱 QRIS</span>
                <span>•</span>
                <span class="flex items-center gap-1">🏦 Transfer Bank</span>
                <span>•</span>
                <span class="flex items-center gap-1">👛 E-Wallet</span>
            </div>
        </div>

        {{-- Polling status (jika sudah pernah bayar tapi belum konfirmasi) --}}
        @if($activeTx)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5"
             id="area-polling">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3 text-sm">Status Pembayaran Terakhir</h3>
            <div class="flex items-center justify-between">
                <span id="badge-status-px"
                      class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                    Menunggu...
                </span>
                <button type="button"
                        onclick="cekStatusPolling()"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    🔄 Refresh Status
                </button>
            </div>
            <p id="teks-status-px" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Memuat status...
            </p>
        </div>

        <script>
        const merchantRef = @json($activeTx->merchant_ref);
        const statusUrl   = @json(route('pelanggan.pakasir.cek-status', ['ref' => $activeTx->merchant_ref]));

        function cekStatusPolling() {
            fetch(statusUrl, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) }
            })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('badge-status-px');
                const teks  = document.getElementById('teks-status-px');
                const map   = {
                    pending : { cls: 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300', label: 'Menunggu', t: 'Menunggu konfirmasi pembayaran...' },
                    paid    : { cls: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300', label: 'Lunas ✅', t: 'Pembayaran berhasil! Halaman akan diperbarui...' },
                    failed  : { cls: 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300', label: 'Gagal', t: 'Pembayaran gagal. Silakan coba kembali.' },
                    expired : { cls: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400', label: 'Kadaluarsa', t: 'Link kadaluarsa. Silakan buat pembayaran baru.' },
                };
                const s = map[data.status] || map['pending'];
                badge.className   = 'px-3 py-1 rounded-full text-xs font-semibold ' + s.cls;
                badge.textContent = s.label;
                teks.textContent  = s.t;

                if (data.is_paid || data.tagihan_lunas) {
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .catch(() => {});
        }

        // Auto-refresh tiap 5 detik
        cekStatusPolling();
        setInterval(cekStatusPolling, 5000);
        </script>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
function setBtnLoading() {
    const btn  = document.getElementById('btn-bayar-pakasir');
    const icon = document.getElementById('btn-bayar-icon');
    const text = document.getElementById('btn-bayar-text');
    btn.disabled      = true;
    btn.classList.add('opacity-75');
    icon.textContent  = '⏳';
    text.textContent  = 'Mengarahkan ke Pakasir...';
}
</script>
@endpush