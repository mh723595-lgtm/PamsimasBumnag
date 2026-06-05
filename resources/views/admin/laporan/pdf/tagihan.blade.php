<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Tagihan Air - PAMSIMAS</title>

<style>
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:10px;
        color:#333;
    }

    .report{
        border:3px solid #1d4ed8;
    }

    /* HEADER */
    .header{
        background:#0f2f78;
        color:white;
        padding:15px;
    }

    .header h1{
        font-size:18px;
        font-weight:bold;
        margin-bottom:4px;
    }

    .header p{
        font-size:9px;
    }

    .meta{
        margin-top:8px;
    }

    .meta span{
        display:inline-block;
        background:#1f4fb3;
        padding:4px 8px;
        margin-right:5px;
        border-radius:3px;
        font-size:8px;
    }

    /* SUMMARY */
    .summary{
        padding:10px;
        border-top:3px solid #3b82f6;
        border-bottom:3px solid #3b82f6;
    }

    .summary table{
        width:100%;
        border-collapse:collapse;
    }

    .summary td{
        width:20%;
        padding:0 5px;
    }

    .summary-box{
        border:1px solid #cbd5e1;
        text-align:center;
        padding:8px;
        background:#f8fafc;
    }

    .summary-label{
        font-size:8px;
        color:#64748b;
    }

    .summary-value{
        font-size:12px;
        font-weight:bold;
        color:#0f2f78;
        margin-top:2px;
    }

    /* SECTION TITLE */
    .section-title{
        background:#0f2f78;
        color:white;
        padding:6px 10px;
        font-size:9px;
        font-weight:bold;
        letter-spacing:1px;
    }

    /* TABLE */
    table.data{
        width:100%;
        border-collapse:collapse;
    }

    table.data thead{
        background:#1d4ed8;
        color:white;
    }

    table.data th{
        padding:8px;
        font-size:8px;
        text-transform:uppercase;
        border:1px solid #1e40af;
    }

    table.data td{
        padding:7px;
        border:1px solid #dbeafe;
        font-size:9px;
    }

    table.data tbody tr:nth-child(even){
        background:#f8fafc;
    }

    .center{
        text-align:center;
    }

    .right{
        text-align:right;
    }

    /* STATUS */
    .status-lunas{
        color:#16a34a;
        font-weight:bold;
    }

    .status-belum{
        color:#d97706;
        font-weight:bold;
    }

    .status-terlambat{
        color:#dc2626;
        font-weight:bold;
    }

    /* FOOTER */
    .footer-area{
        margin-top:15px;
        padding:15px;
        border-top:2px solid #3b82f6;
    }

    .footer-left{
        float:left;
        width:50%;
        font-size:8px;
        color:#64748b;
        line-height:1.6;
    }

    .footer-right{
        float:right;
        width:220px;
        text-align:center;
    }

    .footer-right .date{
        margin-bottom:45px;
        font-size:9px;
    }

    .footer-right .name{
        border-top:1px solid #000;
        padding-top:4px;
        font-weight:bold;
    }

    .clearfix{
        clear:both;
    }

    .system-footer{
        text-align:right;
        font-size:7px;
        color:#94a3b8;
        margin-top:10px;
    }

</style>
</head>

<body>

<div class="report">

    <!-- HEADER -->
    <div class="header">
        <h1>LAPORAN TAGIHAN AIR — PAMSIMAS</h1>
        <p>PAMSIMAS | Desa</p>

        <div class="meta">
            <span>Periode: {{ $namaBulan }} {{ $tahun }}</span>
            <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            <span>{{ $tagihan->count() }} Tagihan</span>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="summary-box">
                        <div class="summary-label">Total Tagihan</div>
                        <div class="summary-value">{{ $summary['total'] }}</div>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <div class="summary-label">Lunas</div>
                        <div class="summary-value">{{ $summary['lunas'] }}</div>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <div class="summary-label">Belum Bayar</div>
                        <div class="summary-value">{{ $summary['belum_bayar'] }}</div>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <div class="summary-label">Total Nominal</div>
                        <div class="summary-value">
                            Rp {{ number_format($summary['nominal'],0,',','.') }}
                        </div>
                    </div>
                </td>

                <td>
                    <div class="summary-box">
                        <div class="summary-label">Terkumpul</div>
                        <div class="summary-value">
                            Rp {{ number_format($summary['terkumpul'],0,',','.') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- TABLE TITLE -->
    <div class="section-title">
        DATA TAGIHAN
    </div>

    <!-- TABLE -->
    <table class="data">
        <thead>
        <tr>
            <th>No</th>
            <th>No. Tagihan</th>
            <th>Nama Pelanggan</th>
            <th>No. Pelanggan</th>
            <th>Pemakaian</th>
            <th>Total Tagihan</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
        </tr>
        </thead>

        <tbody>
        @forelse($tagihan as $i => $t)
            <tr>
                <td class="center">{{ $i+1 }}</td>
                <td>{{ $t->nomor_tagihan }}</td>
                <td>{{ $t->pelanggan->nama_pelanggan }}</td>
                <td class="center">{{ $t->pelanggan->nomor_pelanggan }}</td>
                <td class="center">{{ number_format($t->pemakaian,1) }} m³</td>
                <td class="right">
                    Rp {{ number_format($t->total_tagihan,0,',','.') }}
                </td>
                <td class="center">
                    {{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}
                </td>
                <td class="center">
                    @if($t->status == 'lunas')
                        <span class="status-lunas">LUNAS</span>
                    @elseif($t->status == 'terlambat')
                        <span class="status-terlambat">TERLAMBAT</span>
                    @else
                        <span class="status-belum">BELUM BAYAR</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="center" style="padding:20px">
                    Tidak ada data tagihan untuk periode ini
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer-area">

        <div class="footer-left">
            Laporan ini dibuat secara otomatis oleh Sistem Informasi PAMSIMAS.<br>
            Keabsahan data diverifikasi oleh pengelola.<br>
            Hanya data dengan status valid yang ditampilkan.
        </div>

        <div class="footer-right">
            <div class="date">
                Desa, {{ now()->translatedFormat('d F Y') }}
            </div>

            <div class="name">
                PAMSIMAS
            </div>

            <div>
                Administrator / Pengelola
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="system-footer">
            Dicetak: {{ now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

</div>

</body>
</html>