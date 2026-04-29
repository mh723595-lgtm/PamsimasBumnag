<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pembayaran {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DejaVu Sans',Arial,sans-serif;font-size:10px;color:#1f2937;background:#fff;}
        .header{background:linear-gradient(135deg,#065f46 0%,#059669 60%,#34d399 100%);color:white;padding:18px 22px 14px;}
        .header h1{font-size:17px;font-weight:bold;margin-bottom:3px;}
        .header .sub{font-size:9.5px;opacity:.85;}
        .header .meta{display:flex;gap:10px;margin-top:10px;}
        .header .meta span{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);padding:3px 10px;border-radius:20px;font-size:8.5px;}
        .summary{display:flex;background:#f0fdf4;border-bottom:2px solid #bbf7d0;}
        .si{flex:1;padding:9px 12px;border-right:1px solid #bbf7d0;text-align:center;}
        .si:last-child{border-right:none;}
        .si .l{display:block;font-size:7.5px;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;}
        .si .v{font-size:12px;font-weight:bold;color:#065f46;}
        table{width:100%;border-collapse:collapse;margin-top:0;}
        thead tr{background:#065f46;color:#fff;}
        thead th{padding:8px 9px;text-align:left;font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:.04em;}
        thead th.c{text-align:center;}
        thead th.r{text-align:right;}
        tbody tr{border-bottom:1px solid #f1f5f9;}
        tbody tr:nth-child(even){background:#f0fdf4;}
        tbody td{padding:6.5px 9px;font-size:9.5px;vertical-align:middle;}
        tbody td.c{text-align:center;}
        tbody td.r{text-align:right;font-weight:bold;}
        tbody td.mono{font-family:'DejaVu Sans Mono',monospace;font-size:8px;}
        .badge-konfirmasi{background:#d1fae5;color:#065f46;padding:2px 7px;border-radius:10px;font-size:7.5px;font-weight:bold;}
        .badge-pending{background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:10px;font-size:7.5px;font-weight:bold;}
        .badge-tunai{background:#dbeafe;color:#1e40af;padding:1px 6px;border-radius:8px;font-size:7.5px;}
        .badge-transfer{background:#ede9fe;color:#5b21b6;padding:1px 6px;border-radius:8px;font-size:7.5px;}
        .badge-lainnya{background:#f3f4f6;color:#374151;padding:1px 6px;border-radius:8px;font-size:7.5px;}
        .total-row{background:#065f46!important;}
        .total-row td{color:#fff;padding:8px 9px;font-size:10px;font-weight:bold;}
        .recap{display:flex;gap:10px;padding:10px 0 0;}
        .ri{flex:1;border:1px solid #bbf7d0;border-radius:7px;padding:9px;background:#f0fdf4;}
        .ri .rl{font-size:7.5px;color:#6b7280;text-transform:uppercase;margin-bottom:3px;}
        .ri .rv{font-size:11px;font-weight:bold;color:#065f46;}
        .footer{margin-top:14px;border-top:1px solid #e5e7eb;padding-top:10px;display:flex;justify-content:space-between;align-items:flex-end;}
        .ttd{text-align:center;}
        .ttd .tl{font-size:8.5px;color:#6b7280;margin-bottom:44px;}
        .ttd .tn{font-weight:bold;font-size:9.5px;border-top:1px solid #374151;padding-top:3px;}
        .ttd .tj{font-size:8px;color:#6b7280;}
        .di{font-size:8px;color:#9ca3af;text-align:right;line-height:1.7;}
        .no-data{text-align:center;color:#9ca3af;padding:35px;font-style:italic;}
    </style>
</head>
<body>

<div class="header">
    <h1>LAPORAN PEMBAYARAN AIR — PAMSIMAS</h1>
    <p class="sub">{{ \App\Models\SettingAplikasi::get('nama_sistem','PAMSIMAS') }} | {{ \App\Models\SettingAplikasi::get('nama_desa','Desa') }}, {{ \App\Models\SettingAplikasi::get('kecamatan','') }}</p>
    <div class="meta">
        <span>📅 Periode: {{ $namaBulan }} {{ $tahun }}</span>
        <span>🖨️ Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
        <span>📊 {{ $pembayaran->count() }} Transaksi</span>
    </div>
</div>

<div class="summary">
    <div class="si"><span class="l">Total Transaksi</span><span class="v">{{ $summary['total'] }}</span></div>
    <div class="si"><span class="l">Total Pendapatan</span><span class="v">Rp {{ number_format($summary['nominal'],0,',','.') }}</span></div>
    <div class="si"><span class="l">Tunai</span><span class="v">Rp {{ number_format($summary['tunai'],0,',','.') }}</span></div>
    <div class="si"><span class="l">Transfer</span><span class="v">Rp {{ number_format($summary['transfer'],0,',','.') }}</span></div>
    <div class="si"><span class="l">Rata-rata / Trx</span><span class="v">{{ $summary['total']>0 ? 'Rp '.number_format($summary['nominal']/$summary['total'],0,',','.') : '-' }}</span></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:20px">No</th>
            <th>No. Pembayaran</th>
            <th>Nama Pelanggan</th>
            <th class="c">No. Pelanggan</th>
            <th class="c">No. Tagihan</th>
            <th class="c">Tgl Bayar</th>
            <th class="c">Metode</th>
            <th class="r">Jumlah</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pembayaran as $i => $b)
        <tr>
            <td class="c">{{ $i+1 }}</td>
            <td class="mono">{{ $b->nomor_pembayaran }}</td>
            <td>{{ $b->pelanggan->nama_pelanggan }}</td>
            <td class="c mono">{{ $b->pelanggan->nomor_pelanggan }}</td>
            <td class="c mono">{{ $b->tagihan->nomor_tagihan }}</td>
            <td class="c">{{ $b->tanggal_bayar->format('d/m/Y') }}</td>
            <td class="c"><span class="badge-{{ $b->metode_bayar }}">{{ ucfirst($b->metode_bayar) }}</span></td>
            <td class="r">Rp {{ number_format($b->jumlah_bayar,0,',','.') }}</td>
            <td class="c"><span class="badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="no-data">Tidak ada data pembayaran untuk periode ini</td></tr>
        @endforelse
        @if($pembayaran->count()>0)
        <tr class="total-row">
            <td colspan="7" style="text-align:right;padding-right:12px;">TOTAL PENDAPATAN {{ strtoupper($namaBulan) }} {{ $tahun }}</td>
            <td class="r">Rp {{ number_format($summary['nominal'],0,',','.') }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

@if($pembayaran->count()>0)
@php $byMetode = $pembayaran->groupBy('metode_bayar'); @endphp
<div class="recap">
    @foreach(['tunai'=>'Pembayaran Tunai','transfer'=>'Pembayaran Transfer','lainnya'=>'Metode Lainnya'] as $k=>$l)
    @php $items=$byMetode->get($k,collect()); @endphp
    @if($items->count()>0)
    <div class="ri">
        <p class="rl">{{ $l }} ({{ $items->count() }} transaksi)</p>
        <p class="rv">Rp {{ number_format($items->sum('jumlah_bayar'),0,',','.') }}</p>
    </div>
    @endif
    @endforeach
</div>
@endif

<div class="footer">
    <div style="font-size:8px;color:#9ca3af;max-width:45%;line-height:1.6;">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi PAMSIMAS.</p>
        <p>Keabsahan dapat diverifikasi dengan cap dan tanda tangan pengelola.</p>
        <p>Hanya data dengan status "konfirmasi" yang ditampilkan.</p>
    </div>
    <div class="ttd">
        <p class="tl">{{ \App\Models\SettingAplikasi::get('nama_desa','Desa') }}, {{ now()->format('d F Y') }}</p>
        <p class="tn">{{ \App\Models\SettingAplikasi::get('nama_sistem','PAMSIMAS') }}</p>
        <p class="tj">Bendahara / Pengelola Keuangan</p>
    </div>
    <div class="di">
        <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
        <p>Total: Rp {{ number_format($summary['nominal'],0,',','.') }}</p>
        <p>Dokumen ini dihasilkan sistem</p>
    </div>
</div>

</body>
</html>
