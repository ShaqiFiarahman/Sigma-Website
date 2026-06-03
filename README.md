# SIGMA — Sistem Informasi Gawat Darurat dan Mitigasi Bencana

**SIGMA** adalah platform berbasis web yang dirancang untuk mendukung manajemen tanggap darurat bencana, penyebaran informasi mitigasi, pemetaan posko evakuasi, dan koordinasi relawan secara real-time. Aplikasi ini dibangun menggunakan framework **Laravel 13** dan **Tailwind CSS v4** dengan integrasi **Supabase** untuk autentikasi dan penyimpanan data/media, serta **Google Maps API** untuk visualisasi peta interaktif.


---

## ✨ Fitur Utama (Highlights)

- **Peta Interaktif Sebaran Bencana & Posko**: Visualisasi peta real-time (Google Maps API) untuk melacak lokasi bencana aktif dan posko evakuasi terdekat.
- **Sistem Pelaporan Mandiri**: Warga dapat melaporkan bencana langsung dari tempat kejadian beserta titik koordinat GPS dan bukti foto.
- **Alur Kerja Terintegrasi (Masyarakat ➔ Admin ➔ Relawan)**: Siklus penanganan lengkap dari laporan awal, verifikasi admin, penugasan otomatis/manual relawan, hingga aksi relawan di lapangan.
- **Manajemen Relawan & Penugasan**: Sistem pendaftaran, seleksi keahlian (*MEDIS, SAR, LOGISTIK, KONSUMSI, PSIKOSOSIAL, PENDIDIKAN*), status ketersediaan, serta konfirmasi penerimaan tugas oleh relawan.
- **Manajemen Logistik & Posko**: Pemantauan stok logistik posko pengungsian, kapasitas tersisa, dan kontak darurat posko.
- **Autentikasi Aman & Cloud Storage**: Integrasi modern dengan **Supabase Auth** untuk login aman dan **Supabase Storage** untuk efisiensi penyimpanan gambar.

---

## 👥 Aktor & Peran Pengguna

Sistem ini membagi aksesibilitas fitur berdasarkan 3 peran utama:

1. **Masyarakat (Masyarakat Umum)**
   - **Laporan Bencana**: Melaporkan kejadian bencana di sekitar secara real-time beserta koordinat peta (Google Maps) dan foto kejadian.
   - **Peta Bencana**: Melihat sebaran bencana aktif dan posko evakuasi terdekat secara interaktif.
   - **Informasi Posko (Shelter)**: Mencari posko pengungsian berdasarkan kapasitas, logistik yang tersedia, serta rute terdekat.
   - **Panduan Mitigasi**: Membaca artikel dan panduan tanggap darurat bencana.
   - **Registrasi Relawan**: Mendaftar diri menjadi relawan bencana.

2. **Relawan (Volunteer)**
   - **Manajemen Ketersediaan**: Mengatur status ketersediaan (*available/unavailable*) untuk ditugaskan ke lokasi bencana.
   - **Penugasan Mandiri**: Menerima atau menolak penugasan bencana yang diajukan oleh Admin.
   - **Laporan Aktivitas**: Mengirimkan laporan berkala dari lapangan (dilengkapi deskripsi, lokasi koordinat, dan dokumentasi foto).
   - **Dashboard Relawan**: Akses cepat ke detail penugasan dan notifikasi tugas baru.

3. **Admin (BPBD / BNPB)**
   - **Dashboard Statistik**: Memantau grafik statistik bencana, laporan masuk, relawan aktif, dan kapasitas posko.
   - **Verifikasi Laporan**: Memverifikasi laporan bencana dari masyarakat, mengubah status siaga (*SIAGA 1, SIAGA 2, AWAS*), serta mengkategorikan jenis bencana.
   - **Manajemen Posko (Shelter)**: Mengelola data posko evakuasi (CRUD) termasuk kapasitas tampung, ketersediaan logistik, kontak, dan foto posko.
   - **Manajemen Relawan**: Menyetujui/menolak registrasi relawan, menugaskan relawan yang *available* ke bencana tertentu, dan memantau laporan aktivitas relawan.

---

## 🛠️ Tech Stack & Integrasi

- **Core Framework**: PHP 8.3 & [Laravel 13.x](https://laravel.com)
- **Frontend Engine**: [Tailwind CSS v4.0](https://tailwindcss.com), [Vite](https://vite.dev), & Bootstrap Icons
- **Database & Auth Services**: 
  - PostgreSQL hosted on **Supabase**
  - **Supabase Auth** untuk integrasi otentikasi terpusat
- **File & Media Storage**: **Supabase Storage Buckets** (dengan fallback penyimpanan lokal)
- **Maps & Geolocation**: **Google Maps API** (untuk input titik koordinat bencana dan peta sebaran)

---

## 📋 Prasyarat Sistem

Sebelum menjalankan aplikasi, pastikan mesin Anda telah terpasang:
- **PHP** >= 8.3 (dengan ekstensi pgsql/sqlite, pdo, curl, openssl, dll)
- **Composer** (Dependency Manager untuk PHP)
- **Node.js** & **npm** (untuk build assets Tailwind CSS v4)
- Akun **Supabase** (Postgres DB, Auth, Storage)
- **Google Maps API Key** (untuk peta interaktif)

---

## 🚀 Langkah Instalasi & Menjalankan Projek

Projek ini telah dilengkapi dengan *custom composer commands* untuk mempermudah proses instalasi dan jalannya aplikasi secara lokal.

### 1. Klon Repositori
```bash
git clone <url-repositori-sigma>
cd sigma
```

### 2. Setup Otomatis (Rekomendasi)
Cukup jalankan satu perintah berikut untuk menginstal dependensi PHP/JS, membuat berkas `.env`, men-generate key, menjalankan migrasi database, dan melakukan build aset frontend:
```bash
composer run setup
```

*Atau jika ingin melakukan secara manual:*
```bash
# Instal dependensi PHP
composer install

# Duplikat berkas environment
copy .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Instal dependensi JS & build assets
npm install
npm run build
```

### 3. Konfigurasi Environment File (`.env`)
Buka berkas `.env` dan lengkapi konfigurasi database serta API pihak ketiga berikut:

```env
# Koneksi Database (Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=your-supabase-db-host.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-db-password

# Konfigurasi Supabase Client (Auth & Storage)
SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_ANON_KEY=your-supabase-anon-key
SUPABASE_STORAGE_BUCKET=laporan

# Google Maps API (Untuk Peta Interaktif)
GOOGLE_MAPS_API_KEY=AIzaSy...your-google-maps-key
```

### 4. Menjalankan Server Pengembangan (Dev Server)
Jalankan perintah berikut untuk menjalankan server Laravel, Vite compiler, database queue listener, dan Pail logs logger secara bersamaan:
```bash
composer run dev
```
Setelah berjalan, Anda dapat mengakses aplikasi melalui peramban di **`http://localhost:8000`** (atau port yang tertera pada terminal).

---

## 🗄️ Struktur Basis Data & Model Utama

- [User](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/User.php) (`profiles`): Menyimpan data autentikasi pengguna yang tersinkronisasi dengan Supabase Auth (Admin, Masyarakat, Relawan).
- [Disaster](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/Disaster.php) (`disasters`): Menyimpan data laporan kejadian bencana (tipe bencana, status kesiagaan, lokasi latitude/longitude, foto, pelapor, dan verifikator).
- [Shelter](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/Shelter.php) (`shelters`): Menyimpan info posko pengungsian (nama posko, kapasitas maksimum, sisa kapasitas, daftar logistik terperinci, dan kontak darurat).
- [Volunteer](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/Volunteer.php) (`volunteers`): Menyimpan data profil relawan, keahlian khusus (*MEDIS, SAR, LOGISTIK, KONSUMSI, PSIKOSOSIAL, PENDIDIKAN*), status penugasan bencana, dan ketersediaan relawan.
- [VolunteerReport](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/VolunteerReport.php) (`volunteer_reports`): Laporan berkala/aktivitas yang dikirimkan oleh relawan di lapangan untuk dipantau oleh admin.
- [News](file:///d:/Semester%204/Pemrograman%20Web/SIgma%20Website/sigma/app/Models/News.php) (`news`): Menyimpan artikel edukasi kebencanaan dan berita terkini.

---

## 🧪 Pengujian (Testing)

Untuk memastikan kode berjalan dengan baik, Anda dapat menjalankan unit/feature testing bawaan Laravel dengan perintah:
```bash
composer run test
```

---

## 👥 Anggota Kelompok

Proyek ini dikembangkan sebagai tugas kelompok pemrograman web yang terdiri dari 4 anggota:

| No. | Nama Anggota | NIM | Peran & Kontribusi |
| :---: | :--- | :---: | :--- |
| **1** | Fadel Shaqi Fiarahman | L0124050 | Frontend |
| **2** | Fandhi Ahmad Husen Alghozali | L0124052 | UI-UX Designer |
| **3** | Fathoni Nugraho | L0124055 | Database |
| **4** | Huda Febri Pradana | L0124057 | Backend |

---

## 📄 Lisensi

Proyek akademik ini dilisensikan di bawah **[MIT License](LICENSE)**. Anda bebas menggunakan, memodifikasi, dan mendistribusikan kode ini untuk keperluan pembelajaran dan non-komersial.


