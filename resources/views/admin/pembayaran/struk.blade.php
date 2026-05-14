<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran {{ $pembayaran->nomor_pembayaran }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; background: #fff; }
        .struk { width: 300px; margin: 0 auto; padding: 16px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-solid { border-top: 2px solid #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .row span:last-child { text-align: right; max-width: 55%; }
        .total { font-size: 14px; font-weight: bold; }
        .logo { font-size: 16px; font-weight: bold; letter-spacing: 2px; }
        .badge { display: inline-block; border: 1px solid #000; padding: 2px 8px; border-radius: 4px; font-size: 11px; }
        .no-print { text-align: center; margin-top: 20px; }
        .btn-print { padding: 10px 30px; background: #059669; color: #fff; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
        .btn-close { padding: 10px 30px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; margin-left: 8px; }
        @media print {
            .no-print { display: none; }
            body { width: 300px; }
        }
    </style>
</head>
<body>
<div class="struk">
    {{-- Header --}}
    <div class="center">
        <div class="logo">PAMSIMAS</div>
        <div style="font-size:11px; margin-top:2px;">Air Minum Masyarakat</div>
        <div style="font-size:11px;">Nagari Bayua</div>
    </div>

    <div class="divider-solid"></div>

    <div class="center bold" style="font-size:13px;">STRUK PEMBAYARAN</div>
    <div class="center" style="font-size:11px; margin-top:2px;">{{ $pembayaran->nomor_pembayaran }}</div>

    <div class="divider"></div>

    {{-- Info Pembayaran --}}
    <div class="row"><span>Tanggal</span><span>{{ $pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Metode</span><span class="bold">{{ ucfirst($pembayaran->metode ?? $pembayaran->metode_bayar) }}</span></div>
    <div class="row"><span>Status</span><span class="bold">{{ ucfirst($pembayaran->status) }}</span></div>

    <div class="divider"></div>

    {{-- Info Pelanggan --}}
    <div class="bold" style="margin-bottom:4px;">DATA PELANGGAN</div>
    <div class="row"><span>Nama</span><span>{{ $pembayaran->pelanggan->nama_pelanggan ?? $pembayaran->pelanggan->nama }}</span></div>
    <div class="row"><span>No. Pelanggan</span><span>{{ $pembayaran->pelanggan->nomor_pelanggan ?? '-' }}</span></div>

    <div class="divider"></div>

    {{-- Info Tagihan --}}
    <div class="bold" style="margin-bottom:4px;">RINCIAN TAGIHAN</div>
    <div class="row"><span>No. Tagihan</span><span>{{ $pembayaran->tagihan->nomor_tagihan }}</span></div>
    <div class="row"><span>Periode</span><span>{{ $pembayaran->tagihan->periodeTeks() }}</span></div>
    <div class="row"><span>Pemakaian</span><span>{{ $pembayaran->tagihan->pemakaian }} m³</span></div>
    <div class="row"><span>Tagihan Air</span><span>Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}</span></div>
    @if($pembayaran->tagihan->denda > 0)
    <div class="row"><span>Denda</span><span>Rp {{ number_format($pembayaran->tagihan->denda, 0, ',', '.') }}</span></div>
    @endif

    <div class="divider-solid"></div>

    {{-- Total --}}
    <div class="row total">
        <span>TOTAL BAYAR</span>
        <span>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
    </div>

    <div class="divider-solid"></div>

    {{-- Footer --}}
    <div class="center" style="margin-top:8px; font-size:11px;">
        <div>Terima kasih telah membayar tepat waktu</div>
        <div style="margin-top:4px;">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        @if($pembayaran->dikonfirmasiOleh)
        <div style="margin-top:4px;">Petugas: {{ $pembayaran->dikonfirmasiOleh->name }}</div>
        @endif
    </div>

    <div class="divider"></div>
    <div class="center" style="font-size:10px;">*** SIMPAN STRUK INI ***</div>
</div>

{{-- Tombol cetak --}}
<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    <button class="btn-close" onclick="window.close()">✕ Tutup</button>
</div>

<script>
    // Auto buka dialog cetak saat halaman terbuka
    window.onload = function() {
        setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>