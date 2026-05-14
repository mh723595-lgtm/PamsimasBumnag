{{-- resources/views/admin/pelanggan/create.blade.php --}}
{{-- Gunakan file yang sama untuk edit.blade.php --}}
@extends('layouts.app')
@section('title', isset($pelanggan) ? 'Edit Pelanggan' : 'Tambah Pelanggan')
@section('page_title', isset($pelanggan) ? 'Edit Pelanggan' : 'Tambah Pelanggan')
@section('page_subtitle', isset($pelanggan) ? 'Ubah data pelanggan' : 'Tambah pelanggan baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #peta { height: 320px; width: 100%; border-radius: 12px; z-index: 1; }
</style>
@endpush

@section('content')
<form action="{{ isset($pelanggan) ? route('admin.pelanggan.update', $pelanggan) : route('admin.pelanggan.store') }}"
      method="POST">
    @csrf
    @if(isset($pelanggan)) @method('PUT') @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-sm text-red-700 dark:text-red-300">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── KOLOM KIRI ── --}}
        <div class="space-y-6">

            {{-- Data Identitas --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Data Identitas
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nomor Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_pelanggan"
                                value="{{ old('nomor_pelanggan', $pelanggan->nomor_pelanggan ?? '') }}"
                                placeholder="PLG-001"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nomor_pelanggan') border-red-400 @enderror"
                                required>
                            @error('nomor_pelanggan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. Pelanggan Eksternal</label>
                            <input type="text" name="nomor_pelanggan_external"
                                value="{{ old('nomor_pelanggan_external', $pelanggan->nomor_pelanggan_external ?? '') }}"
                                placeholder="Dari sistem lama"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelanggan"
                            value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan ?? '') }}"
                            placeholder="Nama lengkap"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pelanggan') border-red-400 @enderror"
                            required>
                        @error('nama_pelanggan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. HP</label>
                            <input type="text" name="no_hp"
                                value="{{ old('no_hp', $pelanggan->no_hp ?? '') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. KTP</label>
                            <input type="text" name="no_ktp"
                                value="{{ old('no_ktp', $pelanggan->no_ktp ?? '') }}"
                                placeholder="16 digit NIK"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Status <span class="text-red-500">*</span></label>
                            <select name="status"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="aktif"    {{ old('status', $pelanggan->status ?? 'aktif') == 'aktif'    ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $pelanggan->status ?? '')      == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="tutup"    {{ old('status', $pelanggan->status ?? '')      == 'tutup'    ? 'selected' : '' }}>Tutup</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Tanggal Daftar <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_daftar"
                                value="{{ old('tanggal_daftar', isset($pelanggan) ? $pelanggan->tanggal_daftar->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Meteran --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Data Meteran
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nomor Meteran</label>
                        <input type="text" name="nomor_meteran"
                            value="{{ old('nomor_meteran', $pelanggan->nomor_meteran ?? '') }}"
                            placeholder="MTR-2024-001"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nomor_meteran') border-red-400 @enderror">
                        <p class="mt-1 text-xs text-gray-400">Nomor seri meteran air fisik di lapangan</p>
                        @error('nomor_meteran')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Angka Meteran Awal</label>
                        <div class="flex">
                            <input type="number" name="meteran_awal"
                                value="{{ old('meteran_awal', $pelanggan->meteran_awal ?? 0) }}"
                                min="0"
                                class="flex-1 px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="px-3 py-2.5 text-sm bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-700 rounded-r-xl text-gray-500">m³</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Angka meteran saat pertama kali dipasang</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── KOLOM KANAN ── --}}
        <div class="space-y-6">

            {{-- Alamat --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Alamat
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="2" placeholder="Jl. ..."
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 @error('alamat') border-red-400 @enderror"
                            required>{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">RT/RW</label>
                            <input type="text" name="rt_rw"
                                value="{{ old('rt_rw', $pelanggan->rt_rw ?? '') }}"
                                placeholder="001/002"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Desa / Nagari</label>
                            <input type="text" name="desa"
                                value="{{ old('desa', $pelanggan->desa ?? '') }}"
                                placeholder="Nama desa / nagari"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kecamatan</label>
                            <input type="text" name="kecamatan"
                                value="{{ old('kecamatan', $pelanggan->kecamatan ?? '') }}"
                                placeholder="Kecamatan"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kabupaten</label>
                            <input type="text" name="kabupaten"
                                value="{{ old('kabupaten', $pelanggan->kabupaten ?? '') }}"
                                placeholder="Kabupaten"
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Koordinat Peta --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Koordinat Lokasi
                </h3>

                <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-xs text-blue-700 dark:text-blue-300">
                    💡 <strong>Cara pakai:</strong> Klik lokasi di peta untuk menentukan koordinat, atau isi manual di bawah. Marker bisa di-drag setelah diletakkan.
                </div>

                <div id="peta" class="mb-3 border border-gray-200 dark:border-gray-700"></div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" id="btn-lokasi-saya"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all">
                        📍 Lokasi Saya
                    </button>
                    <button type="button" id="btn-hapus-marker"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-all">
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
                        <input type="text" name="latitude" id="input-latitude"
                            value="{{ old('latitude', $pelanggan->latitude ?? '') }}"
                            placeholder="-0.9493"
                            oninput="updateMarkerFromInput()"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Longitude</label>
                        <input type="text" name="longitude" id="input-longitude"
                            value="{{ old('longitude', $pelanggan->longitude ?? '') }}"
                            placeholder="100.3543"
                            oninput="updateMarkerFromInput()"
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono">
                    </div>
                </div>

                <div id="koordinat-info" class="{{ (old('latitude', $pelanggan->latitude ?? null)) ? '' : 'hidden' }} mt-3 p-2.5 bg-gray-50 dark:bg-gray-800 rounded-xl text-xs text-center text-gray-500 font-mono">
                    📍 <span id="koordinat-teks">{{ old('latitude', $pelanggan->latitude ?? '') }}, {{ old('longitude', $pelanggan->longitude ?? '') }}</span>
                </div>
            </div>

        </div>
    </div>

    {{-- Tombol Simpan --}}
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
    const defaultLat = {{ old('latitude', $pelanggan->latitude ?? -0.1277) }};
    const defaultLng = {{ old('longitude', $pelanggan->longitude ?? 100.1746) }};
    const adaKoordinat = {{ (old('latitude', $pelanggan->latitude ?? null)) ? 'true' : 'false' }};

    const peta = L.map('peta').setView([defaultLat, defaultLng], adaKoordinat ? 16 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19
    }).addTo(peta);

    let marker = null;

    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(peta);
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateKoordinat(pos.lat.toFixed(7), pos.lng.toFixed(7));
            });
        }
        peta.setView([lat, lng], 17);
        updateKoordinat(parseFloat(lat).toFixed(7), parseFloat(lng).toFixed(7));
    }

    function updateKoordinat(lat, lng) {
        document.getElementById('input-latitude').value  = lat;
        document.getElementById('input-longitude').value = lng;
        document.getElementById('koordinat-teks').textContent = lat + ', ' + lng;
        document.getElementById('koordinat-info').classList.remove('hidden');
    }

    peta.on('click', function(e) { setMarker(e.latlng.lat, e.latlng.lng); });

    if (adaKoordinat) setMarker(defaultLat, defaultLng);

    function updateMarkerFromInput() {
        const lat = parseFloat(document.getElementById('input-latitude').value);
        const lng = parseFloat(document.getElementById('input-longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) setMarker(lat, lng);
    }

    document.getElementById('btn-lokasi-saya').addEventListener('click', function() {
        if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi.'); return; }
        const btn = this;
        btn.disabled = true;
        btn.textContent = '⏳ Mencari...';
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                setMarker(pos.coords.latitude, pos.coords.longitude);
                btn.disabled = false;
                btn.textContent = '📍 Lokasi Saya';
            },
            function() {
                alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
                btn.disabled = false;
                btn.textContent = '📍 Lokasi Saya';
            }
        );
    });

    document.getElementById('btn-hapus-marker').addEventListener('click', function() {
        if (marker) { peta.removeLayer(marker); marker = null; }
        document.getElementById('input-latitude').value  = '';
        document.getElementById('input-longitude').value = '';
        document.getElementById('koordinat-info').classList.add('hidden');
    });
</script>
@endpush