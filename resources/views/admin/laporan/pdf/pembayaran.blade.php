{{-- resources/views/admin/laporan/pdf/pembayaran.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:10px; color:#1f2937; }
        .header { background:linear-gradient(135deg,#065f46,#10b981); color:white; padding:20px 24px; margin-bottom:16px; }
        .header h1 { font-size:18px; font-weight:bold; margin-bottom:4px; }
        .header .meta { display:flex; gap:16px; margin-top:10px; font-size:9px; }
        .header .meta span { background:rgba(255,255,255,0.15); padding:3px 10px; border-radius:20px; }
        .summary { display:flex; gap:8px; padding:0 24px 12px; }
        .summary-item { flex:1; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:8px 10px; text-align:center; }
        .summary-item .label { font-size:8px; color:#6b7280; display:block; margin-bottom:3px; }
        .summary-item .value { font-size:12px; font-weight:bold; color:#059669; }
        table { width:100%; border-collapse:collapse; }
        thead tr { background:#065f46; color:white; }
        thead th { padding:8px 10px; text-align:left; font-size:9px; font-weight:bold; text-transform:uppercase; }
        thead th.center { text-align:center; }
        thead th.right { text-align:right; }
        tbody tr { border-bottom:1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background:#f0fdf4; }
        tbody td { padding:7px 10px; font-size:9.5px; }
        tbody td.center { text-align:center; }
        tbody td.right { text-align:right; font-weight:bold; }
        .footer { text-align:center; color:#94a3b8; font-size:8px; padding:12px 24px; border-top:1px solid #e2e8f0; margin-top:12px; }
        .ttd { display:flex; justify-content:flex-end; padding:0 24px; margin-top:16px; }
        .ttd-box { text-align:center; width:160px; }
        .ttd-box .place-date { font-size:9px; color:#6b7280; margin-bottom:50px; }
        .ttd-box .name { font-weight:bold; font-size:10px; border-top:1px solid #374151; padding-top:4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMBAYARAN — PAMSIMAS</h1>
        <p>Sistem Air Minum Berbasis Masyarakat</p>
        <div class="meta">
            <span>Periode: {{ $namaBulan }} {{ $tahun }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            <span>Total: {{ $pembayaran->count() }} transaksi</span>
        </div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="label">Total Transaksi</span>
            <span class="value">{{ $summary['total'] }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Total Pendapatan</span>
            <span class="value">Rp {{ number_format($summary['nominal'], 0, ',', '.') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>No. Pembayaran</th>
                <th>Pelanggan</th>
                <th>No. Tagihan</th>
                <th class="center">Tgl Bayar</th>
                <th class="center">Metode</th>
                <th class="right">Jumlah Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaran as $i => $b)
            <tr>
                <td class="center">{{ $i+1 }}</td>
                <td style="font-family:monospace;font-size:8px">{{ $b->nomor_pembayaran }}</td>
                <td>{{ $b->pelanggan->nama_pelanggan }}</td>
                <td style="font-family:monospace;font-size:8px">{{ $b->tagihan->nomor_tagihan }}</td>
                <td class="center">{{ $b->tanggal_bayar->format('d/m/Y') }}</td>
                <td class="center" style="text-transform:capitalize">{{ $b->metode_bayar }}</td>
                <td class="right">Rp {{ number_format($b->jumlah_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#9ca3af">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd">
        <div class="ttd-box">
            <p class="place-date">..................., {{ now()->format('d F Y') }}</p>
            <p class="name">Administrator</p>
            <p style="font-size:8px;color:#6b7280">Pengelola PAMSIMAS</p>
        </div>
    </div>

    <div class="footer">
        Dokumen dicetak otomatis oleh Sistem Informasi PAMSIMAS &bull; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>