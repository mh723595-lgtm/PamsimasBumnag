<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tagihan {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; background: #fff; }
        .header { background: linear-gradient(135deg, #1e4287, #2574e6); color: white; padding: 20px 24px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 10px; opacity: 0.8; }
        .header .meta { display: flex; gap: 24px; margin-top: 12px; font-size: 9px; }
        .header .meta span { background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 20px; }
        .summary { display: flex; gap: 8px; padding: 0 24px 12px; }
        .summary-item { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; text-align: center; }
        .summary-item .label { font-size: 8px; color: #94a3b8; display: block; margin-bottom: 3px; }
        .summary-item .value { font-size: 12px; font-weight: bold; color: #1e40af; }
        .summary-item.green .value { color: #059669; }
        .summary-item.amber .value { color: #d97706; }
        table { width: 100%; border-collapse: collapse; margin: 0 0 12px; }
        thead tr { background: #1e4287; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
        thead th.center { text-align: center; }
        thead th.right { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 7px 10px; font-size: 9.5px; }
        tbody td.center { text-align: center; }
        tbody td.right { text-align: right; font-weight: bold; }
        .status-lunas { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .status-belum { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .status-terlambat { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .footer { text-align: center; color: #94a3b8; font-size: 8px; padding: 12px 24px; border-top: 1px solid #e2e8f0; margin-top: 8px; }
        .no-data { text-align: center; color: #94a3b8; padding: 30px; }
        .ttd { display: flex; justify-content: flex-end; padding: 0 24px; margin-top: 16px; }
        .ttd-box { text-align: center; width: 160px; }
        .ttd-box .place-date { font-size: 9px; color: #6b7280; margin-bottom: 50px; }
        .ttd-box .name { font-weight: bold; font-size: 10px; border-top: 1px solid #374151; padding-top: 4px; }
        .ttd-box .jabatan { font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN TAGIHAN AIR — PAMSIMAS</h1>
        <p>Sistem Air Minum Berbasis Masyarakat</p>
        <div class="meta">
            <span>Periode: {{ $namaBulan }} {{ $tahun }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            <span>Total: {{ $tagihan->count() }} tagihan</span>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-item">
            <span class="label">Total Tagihan</span>
            <span class="value">{{ $summary['total'] }}</span>
        </div>
        <div class="summary-item green">
            <span class="label">Lunas</span>
            <span class="value">{{ $summary['lunas'] }}</span>
        </div>
        <div class="summary-item amber">
            <span class="label">Belum Bayar</span>
            <span class="value">{{ $summary['belum_bayar'] }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Total Nominal</span>
            <span class="value">Rp {{ number_format($summary['nominal'], 0, ',', '.') }}</span>
        </div>
        <div class="summary-item green">
            <span class="label">Terkumpul</span>
            <span class="value">Rp {{ number_format($summary['terkumpul'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>No. Tagihan</th>
                <th>Nama Pelanggan</th>
                <th>No. Pelanggan</th>
                <th class="center">Pemakaian</th>
                <th class="right">Total Tagihan</th>
                <th class="center">Jatuh Tempo</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tagihan as $i => $t)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-family:monospace;font-size:8px">{{ $t->nomor_tagihan }}</td>
                <td>{{ $t->pelanggan->nama_pelanggan }}</td>
                <td class="center">{{ $t->pelanggan->nomor_pelanggan }}</td>
                <td class="center">{{ number_format($t->pemakaian, 1) }} m³</td>
                <td class="right">Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}</td>
                <td class="center">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                <td class="center">
                    @if($t->status === 'lunas')
                    <span class="status-lunas">Lunas</span>
                    @elseif($t->status === 'terlambat')
                    <span class="status-terlambat">Terlambat</span>
                    @else
                    <span class="status-belum">Belum Bayar</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="no-data">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- TTD --}}
    <div class="ttd">
        <div class="ttd-box">
            <p class="place-date">..................., {{ now()->format('d F Y') }}</p>
            <p class="name">Administrator</p>
            <p class="jabatan">Pengelola PAMSIMAS</p>
        </div>
    </div>

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Informasi PAMSIMAS &bull; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
