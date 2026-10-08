#!/usr/bin/env python3
"""Generate the proposal-ready PAMSIMAS feature guide using only Python's stdlib."""

from datetime import datetime, timezone
from pathlib import Path
from xml.etree import ElementTree as ET
from zipfile import ZIP_DEFLATED, ZipFile


ROOT = Path(__file__).resolve().parent
OUTPUT = ROOT / "Dokumentasi-Fitur-PAMSIMAS.docx"
W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
REL = "http://schemas.openxmlformats.org/package/2006/relationships"
CT = "http://schemas.openxmlformats.org/package/2006/content-types"
CORE = "http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
DC = "http://purl.org/dc/elements/1.1/"
DCTERMS = "http://purl.org/dc/terms/"
XSI = "http://www.w3.org/2001/XMLSchema-instance"

ET.register_namespace("w", W)
ET.register_namespace("r", R)
ET.register_namespace("cp", CORE)
ET.register_namespace("dc", DC)
ET.register_namespace("dcterms", DCTERMS)
ET.register_namespace("xsi", XSI)

NAVY = "123B52"
TEAL = "087E83"
PALE = "EAF4F3"
PALE_BLUE = "EEF4F7"
INK = "253746"
MUTED = "5B6B75"
WHITE = "FFFFFF"


def tag(name):
    return f"{{{W}}}{name}"


def element(parent, name, attrs=None):
    return ET.SubElement(parent, tag(name), attrs or {})


def run(parent, text, *, bold=False, color=INK, size=21, italic=False):
    r = element(parent, "r")
    props = element(r, "rPr")
    element(props, "rFonts", {tag("ascii"): "Aptos", tag("hAnsi"): "Aptos"})
    element(props, "sz", {tag("val"): str(size)})
    element(props, "color", {tag("val"): color})
    if bold:
        element(props, "b")
    if italic:
        element(props, "i")
    for i, line in enumerate(str(text).split("\n")):
        if i:
            element(r, "br")
        node = element(r, "t")
        if line[:1].isspace() or line[-1:].isspace():
            node.set("{http://www.w3.org/XML/1998/namespace}space", "preserve")
        node.text = line
    return r


def paragraph(text="", *, style=None, align=None, before=0, after=100,
              color=INK, bold=False, size=21, italic=False, keep=False):
    p = ET.Element(tag("p"))
    props = element(p, "pPr")
    if style:
        element(props, "pStyle", {tag("val"): style})
    spacing = element(props, "spacing", {
        tag("before"): str(before),
        tag("after"): str(after),
        tag("line"): "280",
        tag("lineRule"): "auto",
    })
    if align:
        element(props, "jc", {tag("val"): align})
    if keep:
        element(props, "keepNext")
    if text:
        run(p, text, bold=bold, color=color, size=size, italic=italic)
    return p


def heading(text, level=1):
    return paragraph(text, style=f"Heading{level}", before=220 if level == 1 else 140,
                     after=100, color=NAVY if level == 1 else TEAL,
                     bold=True, size=30 if level == 1 else 24, keep=True)


def bullet(text):
    p = paragraph(after=55)
    props = p.find(tag("pPr"))
    element(props, "ind", {tag("left"): "360", tag("hanging"): "240"})
    run(p, "•  ", bold=True, color=TEAL, size=21)
    run(p, text, size=21)
    return p


def cell_text(cell, text, *, header=False, shade=None, size=17):
    p = element(cell, "p")
    pp = element(p, "pPr")
    element(pp, "spacing", {tag("before"): "35", tag("after"): "35", tag("line"): "230"})
    run(p, text, bold=header, color=WHITE if header else INK, size=size)


def table(headers, rows, widths=None, *, font_size=16):
    tbl = ET.Element(tag("tbl"))
    props = element(tbl, "tblPr")
    element(props, "tblW", {tag("w"): "0", tag("type"): "auto"})
    element(props, "tblLayout", {tag("type"): "fixed"})
    borders = element(props, "tblBorders")
    for side in ("top", "left", "bottom", "right", "insideH", "insideV"):
        element(borders, side, {
            tag("val"): "single",
            tag("sz"): "4",
            tag("space"): "0",
            tag("color"): "D5E1E5",
        })
    if widths:
        grid = element(tbl, "tblGrid")
        for width in widths:
            element(grid, "gridCol", {tag("w"): str(width)})
    for row_idx, values in enumerate([headers] + rows):
        tr = element(tbl, "tr")
        if row_idx == 0:
            tr_props = element(tr, "trPr")
            element(tr_props, "tblHeader")
        for col_idx, value in enumerate(values):
            tc = element(tr, "tc")
            tc_props = element(tc, "tcPr")
            if widths and col_idx < len(widths):
                element(tc_props, "tcW", {
                    tag("w"): str(widths[col_idx]),
                    tag("type"): "dxa",
                })
            if row_idx == 0:
                shade = NAVY
            elif row_idx % 2 == 0:
                shade = PALE_BLUE
            else:
                shade = WHITE
            element(tc_props, "shd", {tag("fill"): shade, tag("val"): "clear"})
            cell_text(tc, value, header=row_idx == 0, shade=shade, size=font_size)
    return tbl


def callout(title, text):
    tbl = table(["CATATAN"], [[f"{title}\n{text}"]], [9360], font_size=18)
    first = tbl.find(tag("tr"))
    cell = first.findall(tag("tc"))[0]
    cell.find(tag("tcPr")).find(tag("shd")).set(tag("fill"), TEAL)
    for p in cell.findall(tag("p")):
        for r in p.findall(tag("r")):
            rpr = r.find(tag("rPr"))
            rpr.find(tag("color")).set(tag("val"), WHITE)
    return tbl


def page_break():
    p = ET.Element(tag("p"))
    r = element(p, "r")
    element(r, "br", {tag("type"): "page"})
    return p


def build_document():
    body = []

    cover = paragraph(before=1500, after=250, align="center")
    run(cover, "PAMSIMAS", bold=True, color=TEAL, size=26)
    body.append(cover)
    title = paragraph(after=180, align="center", color=NAVY, bold=True, size=42)
    run(title, "DOKUMENTASI FITUR\nSISTEM PAMSIMAS", bold=True, color=NAVY, size=42)
    body.append(title)
    subtitle = paragraph(after=400, align="center", color=MUTED, size=24)
    run(subtitle, "Bahan proposal solusi pengelolaan layanan air bersih", color=MUTED, size=24)
    body.append(subtitle)
    accent = paragraph(before=450, after=100, align="center", color=TEAL, bold=True, size=24)
    run(accent, "OPERASIONAL • PELANGGAN • TRANSPARANSI", bold=True, color=TEAL, size=24)
    body.append(accent)
    edition = paragraph(before=2400, after=80, align="center", color=MUTED, size=20)
    run(edition, "Dokumen proposal-ready | Bahasa Indonesia", color=MUTED, size=20)
    body.append(edition)
    examined = paragraph(align="center", color=MUTED, size=19)
    run(examined, "Ruang lingkup: fitur yang terverifikasi pada source code cabang main", color=MUTED, size=19)
    body.append(examined)

    body.append(page_break())
    body.append(heading("Daftar Isi"))
    for item in (
        "1. Ringkasan Eksekutif dan Tujuan",
        "2. Profil Pengguna dan Hak Akses",
        "3. Portal Publik dan Dashboard Admin",
        "4. Data Master dan Penugasan Wilayah",
        "5. Pencatatan Meter dan Perhitungan Tagihan",
        "6. Denda Keterlambatan",
        "7. Pembayaran dan Penagihan",
        "8. Portal Pelanggan, Pengaduan, dan Fitur Petugas",
        "9. Laporan, Notifikasi, dan Audit Trail",
        "10. Pengaturan, Pengalaman Penggunaan, dan Teknologi",
        "11. Ringkasan Fitur per Peran",
        "12. Modul – Kapabilitas – Manfaat",
        "13. Nilai bagi Klien serta Batasan dan Prasyarat",
        "Catatan Sumber dan Integrasi",
    ):
        body.append(bullet(item))
    body.append(paragraph(
        "Daftar isi ini merangkum susunan bab. Nomor halaman tercantum pada footer dan menyesuaikan "
        "aplikasi pembuka dokumen.",
        before=170, after=0, color=MUTED, size=18, italic=True,
    ))

    body.append(page_break())
    body.extend([
        heading("1. Ringkasan Eksekutif dan Tujuan"),
        paragraph(
            "PAMSIMAS adalah aplikasi web untuk mengelola proses layanan air bersih berbasis masyarakat "
            "dalam satu alur kerja: pendataan pengguna dan pelanggan, penugasan petugas, pencatatan "
            "pemakaian meter, pembentukan tagihan, pencatatan pembayaran, pengaduan, serta pemantauan "
            "operasional. Aplikasi membedakan akses Admin, Petugas, dan Pelanggan agar pekerjaan "
            "administratif, lapangan, dan layanan mandiri memiliki ruang kerja yang sesuai."
        ),
        paragraph(
            "Tujuan solusi adalah membantu pengelola menata data dan proses secara konsisten, "
            "memperjelas dasar perhitungan tagihan, memudahkan pelanggan melihat informasi layanan, "
            "serta menyediakan ringkasan dan laporan untuk pemantauan. Ketersediaan manfaat operasional "
            "bergantung pada kelengkapan konfigurasi, data awal, dan kedisiplinan pengguna."
        ),
        heading("2. Profil Pengguna dan Hak Akses"),
        paragraph(
            "Aplikasi menyediakan login dan logout serta pembatasan halaman berdasarkan peran. "
            "Pendaftaran mandiri tersedia bagi Pelanggan dan Petugas; akun baru belum aktif dan menunggu "
            "pemeriksaan Admin. Admin dapat menyetujui dengan catatan opsional atau menolak dengan alasan "
            "wajib, lalu sistem menyimpan status dan mengirim notifikasi dalam aplikasi kepada pendaftar."
        ),
        table(
            ["Peran", "Ruang kerja utama", "Kontribusi operasional"],
            [
                ["Admin", "Dashboard, data master, approval, penugasan, tagihan, pembayaran, pengaduan, laporan, pengaturan, notifikasi, log.",
                 "Mengendalikan konfigurasi, alur layanan, dan pemantauan lintas proses."],
                ["Petugas", "Dashboard, pelanggan yang ditugaskan, meteran, riwayat kerja, pengaduan, peta pelanggan.",
                 "Mendukung pencatatan lapangan dan tindak lanjut pengaduan."],
                ["Pelanggan", "Dashboard, tagihan, detail dan riwayat tagihan, pengaduan.",
                 "Mengakses informasi tagihan dan menyampaikan masalah kepada pengelola."],
                ["Pengunjung", "Landing publik, akses ke pendaftaran dan login.",
                 "Mengenal informasi layanan dan memulai akses akun."],
            ],
            [1200, 4080, 4080],
            font_size=16,
        ),
        callout(
            "Batas akses yang perlu diketahui",
            "Kode yang diperiksa menyediakan portal tagihan dan riwayat bagi pelanggan, tetapi alur "
            "pembuatan transaksi Midtrans berada pada area pembayaran Admin. Jangan menawarkan checkout "
            "mandiri pelanggan sebagai fitur yang telah tersedia.",
        ),
    ])

    body.extend([
        heading("3. Portal Publik dan Dashboard Admin"),
        paragraph(
            "Landing publik memberikan informasi ringkas tentang sistem dan akses menuju pendaftaran "
            "atau login. Setelah masuk, Dashboard Admin menyajikan indikator pelanggan aktif; jumlah "
            "tagihan bulan berjalan, lunas, serta belum dibayar; pendapatan bulan berjalan; pengaduan "
            "berstatus baru; dan total pemakaian. Visualisasi tren pendapatan serta pemakaian mencakup "
            "enam bulan, dilengkapi ringkasan status tagihan dan daftar tagihan serta pengaduan terbaru."
        ),
        paragraph(
            "Ringkasan tersebut membantu pengelola melihat beban layanan dan kondisi penagihan secara "
            "cepat. Angka dashboard mengikuti data yang telah dicatat di aplikasi; dashboard bukan "
            "pengganti rekonsiliasi atau verifikasi transaksi.",
        ),
        heading("4. Data Master dan Penugasan Wilayah"),
        paragraph(
            "Admin mengelola akun/pengguna, data pelanggan, data petugas, dan wilayah/Jorong. Data "
            "pelanggan dapat memuat identitas pelanggan dan meter, alamat/kontak, status aktif, wilayah, "
            "serta koordinat lintang-bujur bila telah tersedia. Data petugas dan Jorong juga memiliki "
            "status, sehingga pengelola dapat menata cakupan kerja dan mengelola data yang tidak aktif."
        ),
        paragraph(
            "Penugasan mendukung penetapan petugas ke Jorong dengan informasi periode, catatan, dan "
            "sakelar aktif/nonaktif. Pelanggan dapat dipasangkan kepada petugas secara individual atau "
            "secara massal; halaman detail penugasan menampilkan informasi petugas, cakupan wilayah, "
            "dan daftar pelanggan terkait. Pengelola dapat menggunakan struktur ini untuk memperjelas "
            "pembagian area kerja.",
        ),
        heading("5. Pencatatan Meter dan Perhitungan Tagihan"),
        paragraph(
            "Petugas melihat daftar pelanggan aktif yang ditugaskan langsung kepadanya, memilih periode "
            "bulan dan tahun, lalu mencatat tanggal pembacaan, angka akhir, dan catatan opsional. Angka "
            "awal diisi dari pembacaan periode sebelumnya yang tersedia atau dari angka meter awal "
            "pelanggan. Angka akhir harus berupa angka dan tidak boleh lebih kecil daripada angka awal; "
            "pemakaian dihitung dari selisih keduanya."
        ),
        paragraph(
            "Satu pelanggan tidak dapat memiliki entri meter ganda untuk periode yang sama melalui "
            "validasi aplikasi. Foto bukti meter bersifat opsional; unggahan menerima berkas gambar hingga "
            "5 MB. Setelah pembacaan tersimpan, aplikasi membuat tagihan untuk periode itu secara "
            "otomatis dan mencatat aktivitas input meter.",
        ),
        heading("Struktur Tarif Progresif"),
        table(
            ["Komponen", "Dasar perhitungan", "Pengelolaan"],
            [
                ["Blok 0–10 m³", "Tarif flat untuk blok pertama.", "Nilai dapat dikonfigurasi pada pengaturan tarif."],
                ["Blok 11–20 m³", "Tarif per m³ untuk volume pada rentang ini.", "Nilai per m³ dapat dikonfigurasi."],
                ["Di atas 20 m³", "Tarif per m³ untuk volume yang melampaui 20 m³.", "Nilai per m³ dapat dikonfigurasi."],
                ["Administrasi", "Biaya tetap ditambahkan ke biaya pemakaian.", "Nilai dapat dikonfigurasi, termasuk nol."],
            ],
            [1750, 4300, 3310],
        ),
        paragraph(
            "Rincian tagihan menampilkan pemakaian, komponen tarif, biaya administrasi, dan total. "
            "Nomor tagihan dibuat berformat periode dan nomor urut unik. Status yang digunakan adalah "
            "belum bayar, terlambat, dan lunas. Tanggal jatuh tempo pada tagihan yang dihasilkan saat ini "
            "mengacu pada akhir bulan periode; kebijakan tanggal yang tersimpan di pengaturan perlu "
            "dikonfirmasi sebelum digunakan sebagai dasar janji operasional.",
        ),
    ])

    body.extend([
        heading("6. Denda Keterlambatan"),
        paragraph(
            "Admin dapat mengaktifkan atau menonaktifkan denda, menentukan persentase per bulan, nilai "
            "minimum, batas maksimum sebagai persentase dari tagihan, serta masa tenggang (grace period). "
            "Proses penerapan tersedia sebagai tindakan manual dari halaman pengaturan. Tagihan yang "
            "memenuhi kondisi diperbarui menjadi terlambat, nilai denda dan total bayar dihitung ulang, "
            "dan pelanggan menerima notifikasi aplikasi ketika denda baru dikenakan atau berubah."
        ),
        paragraph(
            "Rumus denda menggunakan persentase tagihan pokok berdasarkan lama keterlambatan dalam "
            "kelipatan bulan, dengan batas minimum dan maksimum yang dikonfigurasi. Penjadwalan otomatis "
            "proses denda tidak dijadikan klaim dalam dokumen ini; operator perlu menjalankan proses "
            "sesuai kebijakan dan tata kerja yang disepakati.",
        ),
        heading("7. Pembayaran dan Penagihan"),
        paragraph(
            "Admin memperoleh daftar tagihan terbuka (belum bayar atau terlambat), dapat mencatat "
            "pembayaran tunai atau mengonfirmasi pembayaran, dan dapat membuka/mencetak struk. Setelah "
            "pembayaran dikonfirmasi, status tagihan berubah menjadi lunas dan pelanggan menerima "
            "notifikasi dalam aplikasi."
        ),
        paragraph(
            "Kode aplikasi juga menyediakan pembuatan transaksi Midtrans Snap dan penerimaan notifikasi "
            "status transaksi untuk mencatat pembayaran transfer serta memperbarui status tagihan. "
            "Kanal yang dapat dipilih, keberhasilan transaksi, dan mode produksi bergantung pada "
            "konfigurasi Midtrans serta kredensial deployment yang valid (server key, client key, dan "
            "pengaturan lingkungan). Pembayaran online bukan layanan yang otomatis aktif hanya dengan "
            "memasang aplikasi.",
        ),
        callout(
            "Prasyarat pembayaran online",
            "Sebelum operasional, pemilik deployment perlu memasang kredensial yang sesuai, menyelesaikan "
            "konfigurasi akun dan kanal pada Midtrans, serta menguji alur notifikasi pada lingkungan "
            "yang dituju. Jangan menampilkan kredensial di dokumen proposal atau repositori publik.",
        ),
        heading("8. Portal Pelanggan, Pengaduan, dan Fitur Petugas"),
        heading("Portal Pelanggan", 2),
        paragraph(
            "Pelanggan dapat melihat dashboard, daftar tagihan, rincian tagihan, riwayat, serta status "
            "tagihan dan denda yang relevan. Portal menyediakan pelacakan informasi; perubahan status "
            "pembayaran dilakukan melalui proses konfirmasi yang tersedia bagi Admin atau notifikasi "
            "Midtrans yang terkonfigurasi."
        ),
        heading("Pengaduan Pelanggan", 2),
        paragraph(
            "Pelanggan dapat mengirim pengaduan dengan nomor tiket, judul, deskripsi, kategori kerusakan, "
            "tagihan, pelayanan, atau lainnya. Foto opsional menerima JPG/JPEG/PNG/WEBP hingga 2 MB. "
            "Setiap pengaduan memiliki status dan prioritas."
        ),
        paragraph(
            "Admin dapat mencari berdasarkan judul, nomor tiket, atau nama pelanggan, serta memfilter "
            "status, kategori, dan prioritas; Admin memperbarui status/prioritas dan memberikan "
            "tanggapan. Petugas dapat membuka daftar/detail pengaduan dan memprosesnya menjadi "
            "diproses atau selesai. Status yang tersedia adalah baru, diproses, selesai, dan ditolak. "
            "Pembaruan oleh Admin mengirim notifikasi kepada pelanggan; pencatatan tindakan petugas "
            "tersedia pada log aktivitas.",
        ),
        heading("Ruang Kerja Petugas dan Peta", 2),
        paragraph(
            "Petugas memiliki dashboard, daftar pelanggan yang ditugaskan, riwayat pekerjaan/pembacaan, "
            "serta ruang kerja pengaduan. Peta pelanggan menggunakan Leaflet dengan lapisan Google Maps "
            "Hybrid. Peta menampilkan daftar pelanggan dengan koordinat dan daftar yang belum memiliki "
            "koordinat, marker berwarna menurut kondisi aktif/tunggakan/nonaktif, fokus lokasi dari "
            "daftar, serta tautan untuk membuka Google Maps. Peta memerlukan koordinat awal yang memadai "
            "dan akses jaringan ke layanan peta.",
        ),
    ])

    body.extend([
        heading("9. Laporan, Notifikasi, dan Audit Trail"),
        paragraph(
            "Laporan tagihan per bulan/tahun merangkum jumlah tagihan, lunas, belum bayar/terlambat, "
            "nominal, dan realisasi yang terkumpul. Laporan pembayaran menampilkan jumlah transaksi, "
            "nominal, serta porsi tunai dan transfer. Laporan pemakaian menampilkan pelanggan aktif, "
            "jumlah pembacaan, volume, dan rata-rata. Laporan tagihan dan pembayaran dapat diekspor ke "
            "PDF; ekspor PDF untuk laporan pemakaian tidak dinyatakan sebagai fitur tersedia."
        ),
        paragraph(
            "Notifikasi tersedia sebagai daftar dalam aplikasi, dengan tindakan menandai satu notifikasi "
            "atau semua sebagai dibaca dan menghapus notifikasi yang sudah dibaca. Pemicu yang "
            "terverifikasi mencakup approval/penolakan registrasi, konfirmasi pembayaran, perubahan "
            "pengaduan oleh Admin, dan pengenaan/perubahan denda. Notifikasi ini bukan pemberitahuan "
            "SMS atau email."
        ),
        paragraph(
            "Audit trail mencatat aktivitas operasional tertentu, antara lain input meter, konfirmasi "
            "pembayaran, pembuatan/perubahan pengaduan, persetujuan atau penolakan registrasi, dan "
            "perubahan pengaturan. Log admin mendukung peninjauan aktivitas. Cakupan pencatatan "
            "bergantung pada titik proses yang secara eksplisit menulis log di aplikasi.",
        ),
        heading("10. Pengaturan, Pengalaman Penggunaan, dan Teknologi"),
        paragraph(
            "Admin dapat mengelola identitas sistem dan wilayah operasional, alamat/kontak, tarif "
            "progresif dan biaya administrasi, parameter tanggal jatuh tempo serta pengingat, konfigurasi "
            "denda, dan sakelar notifikasi. Nilai awal bukan kebijakan final; pengelola perlu "
            "menyesuaikannya dengan keputusan layanan setempat."
        ),
        table(
            ["Area teknologi", "Implementasi / prasyarat"],
            [
                ["Antarmuka", "Template Blade pada Laravel; tampilan menggunakan Tailwind CSS dan mendukung tata letak responsif."],
                ["Preferensi tampilan", "Mode terang/gelap tersedia pada antarmuka."],
                ["Visualisasi", "Dashboard memakai Chart.js untuk ringkasan dan tren."],
                ["Data", "README proyek menyebut MySQL; koneksi dan kredensial database ditentukan di konfigurasi lingkungan deployment."],
                ["Ekspor", "DomPDF digunakan untuk ekspor PDF laporan tagihan dan pembayaran."],
                ["Pembayaran", "Midtrans Snap diimplementasikan pada alur Admin; aktivasi memerlukan kredensial dan pengaturan akun/lingkungan."],
                ["Peta", "Leaflet menampilkan lapisan Google Maps Hybrid; akses jaringan dan layanan peta menjadi prasyarat."],
            ],
            [1900, 7460],
            font_size=16,
        ),
        paragraph(
            "Pemisahan antara implementasi aplikasi dan prasyarat deployment penting untuk perencanaan "
            "proposal: kode menyediakan alur yang disebutkan, sementara kredensial, akses jaringan, "
            "konfigurasi penyimpanan, dan kesiapan data tetap perlu disediakan dan diuji pada lingkungan "
            "klien.",
        ),
        heading("11. Ringkasan Fitur per Peran"),
        table(
            ["Peran", "Fitur yang tersedia"],
            [
                ["Admin", "Dashboard dan monitoring; pengguna, pelanggan, petugas, wilayah/Jorong; approval registrasi; penugasan; tagihan dan pembayaran; pengaduan; laporan/PDF; notifikasi; log; pengaturan."],
                ["Petugas", "Dashboard; daftar pelanggan yang ditugaskan; pencatatan meter dan riwayat; pengaduan; peta pelanggan."],
                ["Pelanggan", "Dashboard; daftar/detail tagihan; riwayat; status denda; membuat, melihat, dan mengikuti perkembangan pengaduan."],
                ["Pengunjung", "Melihat landing publik serta menuju halaman login atau pendaftaran."],
            ],
            [1450, 7910],
            font_size=17,
        ),
        heading("12. Modul – Kapabilitas – Manfaat"),
        table(
            ["Modul", "Kapabilitas", "Manfaat operasional"],
            [
                ["Akun & approval", "Login/logout, pendaftaran pelanggan/petugas, antrean approval.", "Akses terkontrol dan verifikasi akun sebelum digunakan."],
                ["Dashboard Admin", "Indikator, tren 6 bulan, status serta data terbaru.", "Membantu pemantauan kondisi layanan secara ringkas."],
                ["Data master", "Pengguna, pelanggan, petugas, Jorong, status, koordinat.", "Data operasional lebih tertata sebagai acuan kerja."],
                ["Penugasan", "Petugas ke wilayah; pelanggan ke petugas; periode/status/catatan.", "Memperjelas cakupan petugas dan tanggung jawab layanan."],
                ["Meter & tagihan", "Bacaan periode, bukti foto, tarif progresif, rincian tagihan.", "Mendukung konsistensi pencatatan serta transparansi komponen biaya."],
                ["Denda", "Konfigurasi kebijakan dan proses manual; notifikasi perubahan.", "Mendukung penerapan kebijakan keterlambatan yang terukur."],
                ["Pembayaran", "Pembayaran tunai, konfirmasi, struk, opsi Midtrans Snap.", "Memudahkan pencatatan penerimaan dan penelusuran status."],
                ["Portal pelanggan", "Dashboard, tagihan, rincian, riwayat, pengaduan.", "Memberi akses informasi dan kanal komunikasi kepada pelanggan."],
                ["Pengaduan", "Nomor tiket, kategori, prioritas, tanggapan, status.", "Membantu pencatatan dan tindak lanjut masalah layanan."],
                ["Peta petugas", "Lokasi berkoordinat, daftar tanpa koordinat, fokus dan Google Maps.", "Membantu orientasi lokasi untuk kegiatan lapangan."],
                ["Laporan", "Tagihan, pembayaran, pemakaian; PDF untuk dua jenis laporan.", "Mendukung rekap berkala dan bahan evaluasi manajerial."],
                ["Notifikasi & log", "Inbox aplikasi dan rekam aktivitas terpilih.", "Meningkatkan keterlacakan perubahan serta respons layanan."],
                ["Pengaturan", "Identitas, kontak, tarif, jatuh tempo/pengingat, denda, notifikasi.", "Memberi ruang penyesuaian parameter layanan oleh Admin."],
            ],
            [1650, 4030, 3680],
            font_size=15,
        ),
    ])

    body.extend([
        heading("13. Nilai bagi Klien serta Batasan dan Prasyarat"),
        heading("Nilai dan manfaat yang dituju", 2),
        bullet("Efisiensi operasional: alur data pelanggan, bacaan, tagihan, pembayaran, dan pengaduan tersedia dalam aplikasi."),
        bullet("Akurasi pencatatan dan billing: validasi angka meter, pencegahan entri periode ganda, dan perhitungan berdasarkan konfigurasi tarif."),
        bullet("Transparansi tagihan: rincian blok tarif, administrasi, pemakaian, status, dan denda dapat ditinjau."),
        bullet("Percepatan layanan: kanal pengaduan bertiket, daftar tindak lanjut, dan notifikasi status membantu koordinasi."),
        bullet("Monitoring manajerial: dashboard, laporan periode, grafik, dan ringkasan aktivitas mendukung peninjauan."),
        bullet("Akuntabilitas: approval, pembaruan status, pembayaran, serta aktivitas penting tercatat sesuai titik pencatatan aplikasi."),
        heading("Batasan dan prasyarat implementasi", 2),
        bullet("Sediakan database yang didukung dan isi koneksi/aksesnya melalui konfigurasi deployment; README proyek menyebut MySQL."),
        bullet("Siapkan storage publik dan konfigurasi akses file untuk foto meter serta foto pengaduan."),
        bullet("Untuk Midtrans, siapkan kredensial server/client yang sesuai, pengaturan produksi/sandbox, serta konfigurasi notifikasi dan uji transaksi."),
        bullet("Siapkan data pelanggan dan petugas, nomor/identitas meter, angka meter awal, pembagian wilayah/penugasan, serta koordinat lokasi yang valid bila peta akan digunakan."),
        bullet("Tetapkan dan validasi tarif setiap blok, biaya administrasi, tanggal jatuh tempo, pengingat, grace period, dan kebijakan denda sebelum operasional."),
        bullet("Rencanakan pelatihan Admin, Petugas, dan pengguna pelanggan; tetapkan prosedur approval, pembacaan meter, konfirmasi pembayaran, dan tindak lanjut pengaduan."),
        bullet("Konfirmasi perilaku tanggal jatuh tempo dan pengingat dalam proses bisnis; dokumen ini tidak menjanjikan pengiriman pengingat terjadwal."),
        bullet("Kanal layanan eksternal bergantung konfigurasi dan akses jaringan. Notifikasi aplikasi yang tersedia tidak berarti SMS/email aktif."),
        callout(
            "Catatan sumber",
            "Dokumen ini merepresentasikan fitur pada source code cabang main yang diperiksa untuk tugas ini. "
            "Fitur integrasi eksternal—termasuk Midtrans dan layanan peta—bergantung pada konfigurasi "
            "lingkungan, kredensial, jaringan, dan kesiapan deployment.",
        ),
        paragraph(
            "Dokumen ini merangkum kemampuan aplikasi yang diverifikasi untuk membantu diskusi ruang lingkup "
            "proposal. Penetapan kebutuhan, parameter layanan, tata kelola data, dan rencana implementasi "
            "tetap perlu disepakati bersama calon klien.",
            before=160, after=0, color=MUTED, size=19, italic=True,
        ),
    ])

    document = ET.Element(tag("document"))
    body_node = element(document, "body")
    for item in body:
        body_node.append(item)
    sect = element(body_node, "sectPr")
    element(sect, "footerReference", {tag("type"): "default", f"{{{R}}}id": "rId1"})
    element(sect, "pgSz", {tag("w"): "11906", tag("h"): "16838"})
    element(sect, "pgMar", {
        tag("top"): "1000", tag("right"): "1050", tag("bottom"): "1050",
        tag("left"): "1050", tag("header"): "480", tag("footer"): "480",
        tag("gutter"): "0",
    })
    element(sect, "cols", {tag("space"): "425"})
    element(sect, "docGrid", {tag("linePitch"): "360"})
    return ET.tostring(document, encoding="utf-8", xml_declaration=True)


def styles_xml():
    styles = ET.Element(tag("styles"))
    default = element(styles, "style", {tag("type"): "paragraph", tag("default"): "1", tag("styleId"): "Normal"})
    props = element(default, "rPr")
    element(props, "rFonts", {tag("ascii"): "Aptos", tag("hAnsi"): "Aptos"})
    element(props, "sz", {tag("val"): "21"})
    element(props, "color", {tag("val"): INK})
    for level in (1, 2):
        style = element(styles, "style", {
            tag("type"): "paragraph",
            tag("styleId"): f"Heading{level}",
            tag("customStyle"): "1",
        })
        element(style, "basedOn", {tag("val"): "Normal"})
        element(style, "next", {tag("val"): "Normal"})
        element(style, "qFormat")
        ppr = element(style, "pPr")
        element(ppr, "keepNext")
        element(ppr, "outlineLvl", {tag("val"): str(level - 1)})
    return ET.tostring(styles, encoding="utf-8", xml_declaration=True)


def footer_xml():
    footer = ET.Element(tag("ftr"))
    p = element(footer, "p")
    ppr = element(p, "pPr")
    element(ppr, "jc", {tag("val"): "center"})
    run(p, "PAMSIMAS  •  Dokumentasi fitur", color=MUTED, size=16)
    run(p, "     |     Halaman ", color=MUTED, size=16)
    r = element(p, "r")
    element(r, "fldChar", {tag("fldCharType"): "begin"})
    instr = element(p, "r")
    element(instr, "instrText", {"{http://www.w3.org/XML/1998/namespace}space": "preserve"}).text = " PAGE "
    separator = element(p, "r")
    element(separator, "fldChar", {tag("fldCharType"): "separate"})
    result = element(p, "r")
    element(result, "t").text = "1"
    r2 = element(p, "r")
    element(r2, "fldChar", {tag("fldCharType"): "end"})
    return ET.tostring(footer, encoding="utf-8", xml_declaration=True)


def write_docx():
    content_types = ET.Element(f"{{{CT}}}Types")
    ET.SubElement(content_types, f"{{{CT}}}Default", {"Extension": "rels", "ContentType": "application/vnd.openxmlformats-package.relationships+xml"})
    ET.SubElement(content_types, f"{{{CT}}}Default", {"Extension": "xml", "ContentType": "application/xml"})
    ET.SubElement(content_types, f"{{{CT}}}Override", {"PartName": "/word/document.xml", "ContentType": "application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"})
    ET.SubElement(content_types, f"{{{CT}}}Override", {"PartName": "/word/styles.xml", "ContentType": "application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"})
    ET.SubElement(content_types, f"{{{CT}}}Override", {"PartName": "/word/footer1.xml", "ContentType": "application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"})
    ET.SubElement(content_types, f"{{{CT}}}Override", {"PartName": "/docProps/core.xml", "ContentType": "application/vnd.openxmlformats-package.core-properties+xml"})

    package_rels = ET.Element(f"{{{REL}}}Relationships")
    ET.SubElement(package_rels, f"{{{REL}}}Relationship", {
        "Id": "rId1",
        "Type": "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument",
        "Target": "word/document.xml",
    })
    doc_rels = ET.Element(f"{{{REL}}}Relationships")
    ET.SubElement(doc_rels, f"{{{REL}}}Relationship", {
        "Id": "rId1",
        "Type": "http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer",
        "Target": "footer1.xml",
    })
    ET.SubElement(doc_rels, f"{{{REL}}}Relationship", {
        "Id": "rId2",
        "Type": "http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles",
        "Target": "styles.xml",
    })

    core = ET.Element(f"{{{CORE}}}coreProperties")
    ET.SubElement(core, f"{{{DC}}}title").text = "Dokumentasi Fitur PAMSIMAS"
    ET.SubElement(core, f"{{{DC}}}subject").text = "Bahan proposal solusi pengelolaan layanan air bersih"
    ET.SubElement(core, f"{{{DC}}}language").text = "id-ID"
    ET.SubElement(core, f"{{{DC}}}creator").text = "Dokumentasi PAMSIMAS"
    ET.SubElement(core, f"{{{DCTERMS}}}created", {f"{{{XSI}}}type": "dcterms:W3CDTF"}).text = datetime.now(timezone.utc).isoformat()

    with ZipFile(OUTPUT, "w", ZIP_DEFLATED) as archive:
        archive.writestr("[Content_Types].xml", ET.tostring(content_types, encoding="utf-8", xml_declaration=True))
        archive.writestr("_rels/.rels", ET.tostring(package_rels, encoding="utf-8", xml_declaration=True))
        archive.writestr("word/_rels/document.xml.rels", ET.tostring(doc_rels, encoding="utf-8", xml_declaration=True))
        archive.writestr("word/document.xml", build_document())
        archive.writestr("word/styles.xml", styles_xml())
        archive.writestr("word/footer1.xml", footer_xml())
        archive.writestr("docProps/core.xml", ET.tostring(core, encoding="utf-8", xml_declaration=True))
    print(f"Dokumen dibuat: {OUTPUT}")


if __name__ == "__main__":
    write_docx()
