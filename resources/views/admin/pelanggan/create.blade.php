{{-- Gunakan untuk create.blade.php DAN edit.blade.php --}}
@extends('layouts.app')
@section('title', isset($pelanggan) ? 'Edit Pelanggan' : 'Tambah Pelanggan')
@section('page_title', isset($pelanggan) ? 'Edit Pelanggan' : 'Tambah Pelanggan')
@section('page_subtitle', isset($pelanggan) ? 'Ubah data pelanggan' : 'Tambah pelanggan baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#peta { height: 320px; width: 100%; border-radius: 12px; z-index: 1; }
.sel-wilayah { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 36px; }
.sel-wilayah:disabled { opacity: 0.5; cursor: not-allowed; background-color: #f3f4f6; }
.spin { display:inline-block;width:12px;height:12px;border:2px solid #e5e7eb;border-top-color:#3b82f6;border-radius:50%;animation:sp 0.6s linear infinite;vertical-align:middle; }
@keyframes sp{to{transform:rotate(360deg)}}
</style>
@endpush

@section('content')
<form action="{{ isset($pelanggan) ? route('admin.pelanggan.update', $pelanggan) : route('admin.pelanggan.store') }}" method="POST">
@csrf
@if(isset($pelanggan)) @method('PUT') @endif

@if($errors->any())
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-700">
    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

{{-- KOLOM KIRI --}}
<div class="space-y-6">

  {{-- Identitas --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-blue-500"></span> Data Identitas
    </h3>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nomor Pelanggan <span class="text-red-500">*</span></label>
          <input type="text" name="nomor_pelanggan" value="{{ old('nomor_pelanggan', $pelanggan->nomor_pelanggan ?? '') }}" placeholder="PLG-001" required
            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('nomor_pelanggan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. Pelanggan Eksternal</label>
          <input type="text" name="nomor_pelanggan_external" value="{{ old('nomor_pelanggan_external', $pelanggan->nomor_pelanggan_external ?? '') }}" placeholder="Dari sistem lama"
            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
        <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan ?? '') }}" placeholder="Nama lengkap" required
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('nama_pelanggan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. HP</label>
          <input type="text" name="no_hp" value="{{ old('no_hp', $pelanggan->no_hp ?? '') }}" placeholder="08xxxxxxxxxx"
            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. KTP</label>
          <input type="text" name="no_ktp" value="{{ old('no_ktp', $pelanggan->no_ktp ?? '') }}" placeholder="16 digit NIK"
            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Status <span class="text-red-500">*</span></label>
          <select name="status" required class="sel-wilayah w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="aktif"    {{ old('status', $pelanggan->status ?? 'aktif') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $pelanggan->status ?? '')      == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="tutup"    {{ old('status', $pelanggan->status ?? '')      == 'tutup'    ? 'selected' : '' }}>Tutup</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tanggal Daftar <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_daftar" required
            value="{{ old('tanggal_daftar', isset($pelanggan) ? $pelanggan->tanggal_daftar->format('Y-m-d') : now()->format('Y-m-d')) }}"
            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
    </div>
  </div>

  {{-- Meteran --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Data Meteran
    </h3>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nomor Meteran</label>
        <input type="text" name="nomor_meteran" value="{{ old('nomor_meteran', $pelanggan->nomor_meteran ?? '') }}" placeholder="MTR-2024-001"
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <p class="mt-1 text-xs text-gray-400">Nomor seri meteran air fisik di lapangan</p>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Angka Meteran Awal</label>
        <div class="flex">
          <input type="number" name="meteran_awal" value="{{ old('meteran_awal', $pelanggan->meteran_awal ?? 0) }}" min="0"
            class="flex-1 px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <span class="px-3 py-2.5 text-sm bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-700 rounded-r-xl text-gray-500">m³</span>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- KOLOM KANAN --}}
<div class="space-y-6">

  {{-- Alamat --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-amber-500"></span> Alamat
    </h3>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
        <textarea name="alamat" rows="2" placeholder="Nama jalan, nomor rumah, dll..." required
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
        @error('alamat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">RT/RW</label>
        <input type="text" name="rt_rw" value="{{ old('rt_rw', $pelanggan->rt_rw ?? '') }}" placeholder="001/002"
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      {{-- Provinsi --}}
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
          Provinsi <span id="sp-prov" class="hidden"><span class="spin"></span></span>
        </label>
        <select id="sel-prov" name="provinsi"
          class="sel-wilayah w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
          <option value="">-- Pilih Provinsi --</option>
        </select>
        <input type="hidden" id="old-prov" value="{{ old('provinsi', $pelanggan->provinsi ?? '') }}">
      </div>

      {{-- Kabupaten --}}
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
          Kabupaten/Kota <span id="sp-kab" class="hidden"><span class="spin"></span></span>
        </label>
        <select id="sel-kab" name="kabupaten" disabled
          class="sel-wilayah w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
          <option value="">-- Pilih Kabupaten/Kota --</option>
        </select>
        <input type="hidden" id="old-kab" value="{{ old('kabupaten', $pelanggan->kabupaten ?? '') }}">
      </div>

      {{-- Kecamatan --}}
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
          Kecamatan <span id="sp-kec" class="hidden"><span class="spin"></span></span>
        </label>
        <select id="sel-kec" name="kecamatan" disabled
          class="sel-wilayah w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
          <option value="">-- Pilih Kecamatan --</option>
        </select>
        <input type="hidden" id="old-kec" value="{{ old('kecamatan', $pelanggan->kecamatan ?? '') }}">
      </div>

      {{-- Desa --}}
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
          Desa / Nagari <span id="sp-desa" class="hidden"><span class="spin"></span></span>
        </label>
        <select id="sel-desa" name="desa" disabled
          class="sel-wilayah w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
          <option value="">-- Pilih Desa/Nagari --</option>
        </select>
        <input type="hidden" id="old-desa" value="{{ old('desa', $pelanggan->desa ?? '') }}">
      </div>

    </div>
  </div>

  {{-- Koordinat --}}
  <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-rose-500"></span> Koordinat Lokasi
    </h3>
    <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-xs text-blue-700 dark:text-blue-300">
      💡 <strong>Cara pakai:</strong> Klik lokasi di peta, atau isi manual. Marker bisa di-drag.
    </div>
    <div id="peta" class="mb-3 border border-gray-200 dark:border-gray-700"></div>
    <div class="flex flex-wrap gap-2 mb-4">
      <button type="button" id="btn-lokasi"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all">
        📍 Lokasi Saya
      </button>
      <button type="button" id="btn-hapus"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl transition-all">
        🗑️ Hapus Marker
      </button>
      @if(isset($pelanggan) && $pelanggan->hasKoordinat())
      <a href="{{ $pelanggan->googleMapsUrl() }}" target="_blank"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all">
        🗺️ Google Maps
      </a>
      @endif
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Latitude</label>
        <input type="text" name="latitude" id="inp-lat"
          value="{{ old('latitude', $pelanggan->latitude ?? '') }}" placeholder="-0.9493"
          oninput="updateMarkerFromInput()"
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Longitude</label>
        <input type="text" name="longitude" id="inp-lng"
          value="{{ old('longitude', $pelanggan->longitude ?? '') }}" placeholder="100.3543"
          oninput="updateMarkerFromInput()"
          class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono">
      </div>
    </div>
    <div id="koord-info" class="{{ (old('latitude', $pelanggan->latitude ?? null)) ? '' : 'hidden' }} mt-3 p-2.5 bg-gray-50 dark:bg-gray-800 rounded-xl text-xs text-center text-gray-500 font-mono">
      📍 <span id="koord-teks">{{ old('latitude', $pelanggan->latitude ?? '') }}, {{ old('longitude', $pelanggan->longitude ?? '') }}</span>
    </div>
  </div>

</div>
</div>

{{-- Tombol --}}
<div class="mt-6 flex items-center justify-between">
  <a href="{{ route('admin.pelanggan.index') }}"
    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-all">
    ← Kembali
  </a>
  <button type="submit"
    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-all">
    ✓ {{ isset($pelanggan) ? 'Simpan Perubahan' : 'Simpan Pelanggan' }}
  </button>
</div>

</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── PETA ──────────────────────────────────────────────────────
const defLat = {{ old('latitude', $pelanggan->latitude ?? -0.1277) }};
const defLng = {{ old('longitude', $pelanggan->longitude ?? 100.1746) }};
const adaKoord = {{ (old('latitude', $pelanggan->latitude ?? null)) ? 'true' : 'false' }};

const peta = L.map('peta').setView([defLat, defLng], adaKoord ? 16 : 13);
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    attribution: '© Google Maps', maxZoom: 21
}).addTo(peta);

let marker = null;
function setMarker(lat, lng) {
    if (marker) marker.setLatLng([lat, lng]);
    else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(peta);
        marker.on('dragend', e => {
            const p = e.target.getLatLng();
            setKoord(p.lat.toFixed(7), p.lng.toFixed(7));
        });
    }
    peta.setView([lat, lng], 17);
    setKoord(parseFloat(lat).toFixed(7), parseFloat(lng).toFixed(7));
}
function setKoord(lat, lng) {
    document.getElementById('inp-lat').value = lat;
    document.getElementById('inp-lng').value = lng;
    document.getElementById('koord-teks').textContent = lat + ', ' + lng;
    document.getElementById('koord-info').classList.remove('hidden');
}
peta.on('click', e => setMarker(e.latlng.lat, e.latlng.lng));
if (adaKoord) setMarker(defLat, defLng);
function updateMarkerFromInput() {
    const lat = parseFloat(document.getElementById('inp-lat').value);
    const lng = parseFloat(document.getElementById('inp-lng').value);
    if (!isNaN(lat) && !isNaN(lng)) setMarker(lat, lng);
}
document.getElementById('btn-lokasi').addEventListener('click', function() {
    if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi.'); return; }
    this.disabled = true; this.textContent = '⏳ Mencari...';
    const btn = this;
    navigator.geolocation.getCurrentPosition(
        p => { setMarker(p.coords.latitude, p.coords.longitude); btn.disabled=false; btn.textContent='📍 Lokasi Saya'; },
        () => { alert('Tidak dapat mengakses lokasi.'); btn.disabled=false; btn.textContent='📍 Lokasi Saya'; }
    );
});
document.getElementById('btn-hapus').addEventListener('click', function() {
    if (marker) { peta.removeLayer(marker); marker = null; }
    document.getElementById('inp-lat').value = '';
    document.getElementById('inp-lng').value = '';
    document.getElementById('koord-info').classList.add('hidden');
});

// ── DROPDOWN WILAYAH ──────────────────────────────────────────
const API = 'https://www.emsifa.com/api-wilayah-indonesia/api';
const oldProv = document.getElementById('old-prov').value;
const oldKab  = document.getElementById('old-kab').value;
const oldKec  = document.getElementById('old-kec').value;
const oldDesa = document.getElementById('old-desa').value;

function spin(id, show) {
    document.getElementById('sp-' + id).classList.toggle('hidden', !show);
}

function fillSelect(el, data, idKey, nameKey, selected) {
    el.innerHTML = '<option value="">-- Pilih --</option>';
    data.forEach(item => {
        const o = document.createElement('option');
        o.value = item[nameKey];
        o.dataset.id = item[idKey];
        o.textContent = item[nameKey];
        if (item[nameKey] === selected) o.selected = true;
        el.appendChild(o);
    });
    el.disabled = false;
}

function resetBelow(ids) {
    ids.forEach(id => {
        const el = document.getElementById('sel-' + id);
        el.disabled = true;
        el.innerHTML = '<option value="">-- Pilih --</option>';
    });
}

async function loadProv() {
    spin('prov', true);
    const res = await fetch(API + '/provinces.json');
    const data = await res.json();
    fillSelect(document.getElementById('sel-prov'), data, 'id', 'name', oldProv);
    spin('prov', false);
    if (oldProv) {
        const opt = [...document.getElementById('sel-prov').options].find(o => o.value === oldProv);
        if (opt) await loadKab(opt.dataset.id, oldKab);
    }
}

async function loadKab(provId, selected = '') {
    resetBelow(['kab','kec','desa']);
    spin('kab', true);
    const res = await fetch(API + '/regencies/' + provId + '.json');
    const data = await res.json();
    fillSelect(document.getElementById('sel-kab'), data, 'id', 'name', selected);
    spin('kab', false);
    if (selected) {
        const opt = [...document.getElementById('sel-kab').options].find(o => o.value === selected);
        if (opt) await loadKec(opt.dataset.id, oldKec);
    }
}

async function loadKec(kabId, selected = '') {
    resetBelow(['kec','desa']);
    spin('kec', true);
    const res = await fetch(API + '/districts/' + kabId + '.json');
    const data = await res.json();
    fillSelect(document.getElementById('sel-kec'), data, 'id', 'name', selected);
    spin('kec', false);
    if (selected) {
        const opt = [...document.getElementById('sel-kec').options].find(o => o.value === selected);
        if (opt) await loadDesa(opt.dataset.id, oldDesa);
    }
}

async function loadDesa(kecId, selected = '') {
    resetBelow(['desa']);
    spin('desa', true);
    const res = await fetch(API + '/villages/' + kecId + '.json');
    const data = await res.json();
    fillSelect(document.getElementById('sel-desa'), data, 'id', 'name', selected);
    spin('desa', false);
}

document.getElementById('sel-prov').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    opt && opt.dataset.id ? loadKab(opt.dataset.id) : resetBelow(['kab','kec','desa']);
});
document.getElementById('sel-kab').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    opt && opt.dataset.id ? loadKec(opt.dataset.id) : resetBelow(['kec','desa']);
});
document.getElementById('sel-kec').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    opt && opt.dataset.id ? loadDesa(opt.dataset.id) : resetBelow(['desa']);
});

loadProv();
</script>
@endpush