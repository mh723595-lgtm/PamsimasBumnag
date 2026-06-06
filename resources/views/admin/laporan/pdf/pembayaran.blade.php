<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Laporan Pembayaran {{ $namaBulan }} {{ $tahun }}</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'DejaVu Sans',Arial,sans-serif;
    font-size:10px;
    color:#333;
}

.report-container{
    border:2px solid #2449c6;
}

/* HEADER */
.header{
    background:#18398d;
    color:#fff;
    padding:18px;
}

.header h1{
    font-size:18px;
    margin-bottom:5px;
}

.header .sub{
    font-size:9px;
    margin-bottom:10px;
}

.meta span{
    display:inline-block;
    background:#2956d8;
    padding:4px 8px;
    font-size:8px;
    margin-right:5px;
}

/* SUMMARY */
.summary{
    padding:10px;
    border-top:2px solid #2449c6;
    border-bottom:2px solid #2449c6;
}

.summary-table{
    width:100%;
    border-collapse:separate;
    border-spacing:8px 0;
}

.summary-box{
    background:#f3f4f6;
    border:1px solid #d1d5db;
    text-align:center;
    padding:12px;
}

.summary-label{
    display:block;
    font-size:8px;
    color:#6b7280;
    margin-bottom:4px;
}

.summary-value{
    font-size:12px;
    font-weight:bold;
    color:#18398d;
}

/* SECTION TITLE */
.section-title{
    background:#2449c6;
    color:#fff;
    padding:8px 12px;
    font-size:11px;
    font-weight:bold;
}

/* TABLE */
.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table thead tr{
    background:#2956d8;
}

.data-table thead th{
    color:#fff;
    border:1px solid #4c6fe3;
    padding:8px;
    font-size:8px;
    text-transform:uppercase;
}

.data-table tbody td{
    border:1px solid #dbe4ff;
    padding:7px;
    font-size:9px;
}

.data-table tbody tr:nth-child(even){
    background:#f8faff;
}

.c{
    text-align:center;
}

.r{
    text-align:right;
}

.mono{
    font-family:monospace;
}

/* BADGE */
.badge-konfirmasi{
    color:#16a34a;
    font-weight:bold;
}

.badge-pending{
    color:#ea580c;
    font-weight:bold;
}

.badge-tunai{
    color:#2563eb;
    font-weight:bold;
}

.badge-transfer{
    color:#7c3aed;
    font-weight:bold;
}

.badge-lainnya{
    color:#374151;
    font-weight:bold;
}

/* TOTAL */
.total-row{
    background:#2449c6 !important;
}

.total-row td{
    color:white;
    font-weight:bold;
}

/* RECAP */
.recap{
    padding:10px;
}

.recap-table{
    width:100%;
    border-collapse:separate;
    border-spacing:8px 0;
}

.recap-box{
    background:#f3f4f6;
    border:1px solid #d1d5db;
    padding:10px;
    text-align:center;
}

.recap-label{
    font-size:8px;
    color:#6b7280;
}

.recap-value{
    margin-top:5px;
    font-size:11px;
    font-weight:bold;
    color:#18398d;
}

/* FOOTER */
.footer{
    margin-top:15px;
    padding:18px;
    border-top:2px solid #2449c6;
    min-height:120px;
}

.footer-left{
    width:50%;
    float:left;
    font-size:8px;
    color:#6b7280;
    line-height:1.8;
}

.footer-right{
    width:35%;
    float:right;
    text-align:center;
    font-size:9px;
}

.signature-line{
    margin-top:40px;
    margin-bottom:8px;
    border-top:1px solid #333;
}

.clearfix{
    clear:both;
}

.no-data{
    text-align:center;
    padding:30px;
    color:#9ca3af;
}
</style>
</head>

<body>

<div class="report-container">

    <div class="header">
        <h1>LAPORAN PEMBAYARAN AIR — PAMSIMAS</h1>

        <p class="sub">
            {{ \App\Models\SettingAplikasi::get('nama_sistem','PAMSIMAS') }}
            |
            {{ \App\Models\SettingAplikasi::get('nama_desa','Desa') }}
        </p>

        <div class="meta">
            <span>Periode: {{ $namaBulan }} {{ $tahun }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            <span>{{ $pembayaran->count() }} Transaksi</span>
        </div>
    </div>

    <div class="summary">

        <table class="summary-table">
            <tr>

                <td>
                    <div class="summary-box">
                        <span class="summary-label">Total Transaksi</span>
                        <span class="summary-value">{{ $summary['total'] }}</span>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <span class="summary-label">Total Pendapatan</span>
                        <span class="summary-value">
                            Rp {{ number_format($summary['nominal'],0,',','.') }}
                        </span>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <span class="summary-label">Tunai</span>
                        <span class="summary-value">
                            Rp {{ number_format($summary['tunai'],0,',','.') }}
                        </span>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <span class="summary-label">Transfer</span>
                        <span class="summary-value">
                            Rp {{ number_format($summary['transfer'],0,',','.') }}
                        </span>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <span class="summary-label">Rata-rata</span>
                        <span class="summary-value">
                            {{ $summary['total'] > 0 ? 'Rp '.number_format($summary['nominal']/$summary['total'],0,',','.') : '-' }}
                        </span>
                    </div>
                </td>

            </tr>
        </table>

    </div>

    <div class="section-title">
        DATA PEMBAYARAN
    </div>

    <table class="data-table">

        <thead>
            <tr>
                <th width="25">No</th>
                <th>No. Pembayaran</th>
                <th>Nama Pelanggan</th>
                <th>No. Pelanggan</th>
                <th>No. Tagihan</th>
                <th>Tgl Bayar</th>
                <th>Metode</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        @forelse($pembayaran as $i => $b)

            <tr>
                <td class="c">{{ $i+1 }}</td>
                <td class="mono">{{ $b->nomor_pembayaran }}</td>
                <td>{{ $b->pelanggan->nama_pelanggan }}</td>
                <td class="c">{{ $b->pelanggan->nomor_pelanggan }}</td>
                <td class="c">{{ $b->tagihan->nomor_tagihan }}</td>
                <td class="c">{{ $b->tanggal_bayar->format('d/m/Y') }}</td>

                <td class="c">
                    <span class="badge-{{ $b->metode_bayar }}">
                        {{ ucfirst($b->metode_bayar) }}
                    </span>
                </td>

                <td class="r">
                    Rp {{ number_format($b->jumlah_bayar,0,',','.') }}
                </td>

                <td class="c">
                    <span class="badge-{{ $b->status }}">
                        {{ strtoupper($b->status) }}
                    </span>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9" class="no-data">
                    Tidak ada data pembayaran
                </td>
            </tr>

        @endforelse

        @if($pembayaran->count() > 0)

        <tr class="total-row">
            <td colspan="7" class="r">
                TOTAL PEMBAYARAN
            </td>
            <td class="r">
                Rp {{ number_format($summary['nominal'],0,',','.') }}
            </td>
            <td></td>
        </tr>

        @endif

        </tbody>

    </table>

    @if($pembayaran->count()>0)

    @php
        $byMetode = $pembayaran->groupBy('metode_bayar');
    @endphp

    <div class="recap">

        <table class="recap-table">
            <tr>

                @foreach(['tunai'=>'Tunai','transfer'=>'Transfer','lainnya'=>'Lainnya'] as $k=>$label)

                    @php
                        $items = $byMetode->get($k, collect());
                    @endphp

                    @if($items->count())

                    <td>
                        <div class="recap-box">
                            <div class="recap-label">
                                {{ $label }}
                                ({{ $items->count() }} trx)
                            </div>

                            <div class="recap-value">
                                Rp {{ number_format($items->sum('jumlah_bayar'),0,',','.') }}
                            </div>
                        </div>
                    </td>

                    @endif

                @endforeach

            </tr>
        </table>

    </div>

    @endif

    <div class="footer">

        <div class="footer-left">
            <p>Laporan ini dibuat otomatis oleh Sistem Informasi PAMSIMAS.</p>
            <p>Keabsahan data diverifikasi oleh pengelola.</p>
            <p>Hanya data pembayaran valid yang ditampilkan.</p>
        </div>

        <div class="footer-right">

            <p>
                {{ \App\Models\SettingAplikasi::get('nama_desa','Desa') }},
                {{ now()->format('d F Y') }}
            </p>

            <div class="signature-line"></div>

            <strong>
                {{ \App\Models\SettingAplikasi::get('nama_sistem','PAMSIMAS') }}
            </strong>

            <br>

            Bendahara / Pengelola Keuangan

        </div>

        <div class="clearfix"></div>

    </div>

</div>

</body>
</html>