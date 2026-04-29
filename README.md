# 💧 PAMSIMAS SYSTEM

## 📌 Deskripsi  
PAMSIMAS System adalah aplikasi berbasis web yang dibangun menggunakan Laravel untuk membantu pengelolaan layanan air bersih berbasis masyarakat secara digital, terstruktur, dan efisien.

Sistem ini mendukung multi-role (Admin, Petugas, Pelanggan) dengan fitur utama seperti pencatatan meteran air, perhitungan tagihan otomatis menggunakan tarif progresif, pengelolaan pembayaran, serta sistem pengaduan pelanggan.

---

## 🚀 Fitur  

* 🔐 Multi-role Authentication (Admin, Petugas, Pelanggan)  
* 💧 Input & Monitoring Meteran Air  
* 💰 Perhitungan Tagihan Otomatis (Tarif Progresif)  
* 💳 Manajemen Pembayaran  
* 📢 Pengaduan Pelanggan (upload foto opsional)  
* 📊 Dashboard Interaktif (Chart.js)  
* 📄 Export Laporan PDF  
* 🔔 Notifikasi Sistem  
* 🌙 Dark / Light Mode  
* 🧾 Aktivitas Log  

---

## 🛠 Teknologi  

* Laravel (PHP Framework)  
* Blade Template Engine  
* Tailwind CSS (CDN)  
* Alpine.js  
* Chart.js  
* MySQL  

---

## 👩‍💻 Developerx

- Muhammad Haris, NIM: 2330407015
- Habibullah, NIM: 2330407012
- Vigo Alsabri, NIM: 2430407063
- Nayla Nur Fathiniah, NIM: 2330407018

---

## 📌 Cara Menjalankan

# 1. Clone repository
git clone https://github.com/mh723595-lgtm/PamsimasBumnag.git

# 2. Masuk ke folder project
cd PamsimasBumnag

# 3. Install dependency
composer install

# 4. Copy file env
copy .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Setup database di file .env
DB_DATABASE=db_pdam

# 7. Migrasi & seeder
php artisan migrate --seed

# 8. Jalankan server
php artisan serve

# 9. Akses di Browser
http://127.0.0.1:8000

---

## 🔐 Akun Demo

| Role       | Email                  | Password |
|------------|------------------------|----------|
| Admin      | admin@pamsimas.id      | password |
| Petugas    | petugas@pamsimas.id    | password |
| Pelanggan  | pelanggan@pamsimas.id  | password |
