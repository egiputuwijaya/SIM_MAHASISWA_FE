# SIM Mahasiswa Frontend (Web Application UI)

Aplikasi Web Frontend untuk **Sistem Informasi Mahasiswa (SIM)** Universitas Muhammadiyah Bengkulu / Universitas Mercu Buana (UMB). Repositori antarmuka ini mengintegrasikan seluruh fitur akademik, master institusi, manajemen pengguna, pelaporan rekapitulasi data mahasiswa dan dosen, serta pencatatan audit log dengan backend RESTful API.

---

## 📌 Daftar Isi
1. [Fungsi & Fitur Utama](#-fungsi--fitur-utama)
2. [Teknologi & Desain Antarmuka](#-teknologi--desain-antarmuka)
3. [Struktur Folder Proyek](#-struktur-folder-proyek)
4. [Daftar Role & Fitur yang Dimiliki](#-daftar-role--fitur-yang-dimiliki)
5. [Daftar Akun Pengguna Bawaan (Login Credentials)](#-daftar-akun-pengguna-bawaan-login-credentials)
6. [Daftar Halaman & Rute Antarmuka](#-daftar-halaman--rute-antarmuka)
7. [Panduan Menjalankan di Lingkungan Lokal](#-panduan-menjalankan-di-lingkungan-lokal)

---

## 🚀 Fungsi & Fitur Utama

* **Sistem Autentikasi Modern & Sesi Tahan Lama:**
  * Login terhubung ke backend API JWT dengan persistensi sesi 7 hari (*anti-auto logout* saat idle).
  * Auto refresh token ketika token akses kedaluwarsa via endpoint `/auth/refresh`.
  * Tampilan login elegan dengan logo resmi universitas, ilustrasi kampus, dan tombol *show/hide password*.
* **Dashboard Eksekutif & Statistik:**
  * Kartu ringkasan metrik (Total Mahasiswa, Mahasiswa Aktif, Fakultas, Program Studi, Total Pengguna).
  * Informasi sambutan dinamis sesuai nama dan peran pengguna yang sedang login.
* **Master Data & Institusi (Khusus Admin):**
  * **Profil Universitas:** Informasi umum, identitas kampus, alamat, kontak, dan logo.
  * **Fakultas:** Manajemen daftar fakultas, kode fakultas, dan nama fakultas.
  * **Program Studi:** Manajemen prodi, kode prodi, jenjang (D3/S1/S2), dan relasi ke fakultas.
  * **Data Pengguna:** Tambah, edit, ganti status aktif, dan reset password pengguna.
* **Manajemen Data Pokok Mahasiswa:**
  * Daftar mahasiswa dengan pagination, filter prodi, filter status (Aktif, Cuti, Lulus, Drop Out), dan pencarian instan.
  * Form input dan edit mahasiswa terstruktur (NPM, Nama, NIK, Tempat/Tgl Lahir, Jenis Kelamin, Agama, Alamat, Status).
  * Halaman detail profil mahasiswa yang rapi.
* **Pelaporan & Rekapitulasi Berjenjang (Multi-Tier Reporting):**
  * **Rektorat:** Rekapitulasi mahasiswa seluruh universitas.
  * **Dekanat:** Rekapitulasi mahasiswa khusus di bawah naungan fakultasnya.
  * **Kaprodi:** Rekapitulasi mahasiswa khusus program studinya.
  * **Export CSV:** Unduh berkas spreadsheet dengan encoding UTF-8 BOM otomatis.
  * **Cetak PDF:** Format dokumen resmi berkop universitas lengkap dengan pengesahan tanda tangan dinamis (Rektor / Dekan / Kaprodi).
* **Daftar Dosen Fakultas & Program Studi:**
  * Khusus role Dekanat: Menampilkan daftar seluruh dosen di fakultasnya beserta kontak & status aktif.
  * Khusus role Kaprodi: Menampilkan daftar dosen di program studinya.
  * Dukungan **Export CSV** dan **Cetak PDF** resmi.
* **Audit Trail / Audit Log:**
  * Pemantauan riwayat aktivitas mutasi data sistem (*create*, *update*, *delete*) beserta detail IP address dan waktu kejadian.
* **Pengaturan Akun Mandiri:**
  * Setiap user dapat memperbarui nama lengkap, email, nomor HP, serta mengganti kata sandi secara mandiri.

---

## 🎨 Teknologi & Desain Antarmuka

* **Arsitektur:** Native PHP Model-View-Controller (MVC) ringan tanpa beban framework berat (super responsif & cepat diakses).
* **Styling & CSS:** Tailwind CSS (Modern Slate & Corporate Blue Color Palette) + Custom CSS.
* **Reaktivitas Antarmuka:** Alpine.js (Dropdown interaktif, sidebar toggle, modal, dialog konfirmasi).
* **Ikonografi & Tipografi:** FontAwesome 6 Pro/Free & Google Fonts (Inter / Plus Jakarta Sans).
* **Klien HTTP:** `ApiClient.php` berbasis PHP cURL dengan fallback ke `stream_context` serta *auto-retry token*.

---

## 📂 Struktur Folder Proyek

```text
SIM_MAHASISWA_FE/
├── app/                          # Logika Inti Aplikasi (PHP MVC)
│   ├── Controllers/              # Controller Penangan Alur Logika & Tampilan
│   │   ├── Controller.php        # Base Controller (render view & redirect helper)
│   │   ├── AuthController.php    # Autentikasi (login, validasi sesi, logout)
│   │   ├── DashboardController.php # Menampilkan ringkasan statistik & KPI
│   │   ├── MahasiswaController.php # CRUD mahasiswa, detail, filter, & pencarian
│   │   ├── ProfilController.php  # Profil universitas
│   │   ├── PenggunaController.php # Manajemen user & hak akses peran
│   │   ├── FakultasController.php # CRUD master fakultas
│   │   ├── ProgramStudiController.php # CRUD master program studi
│   │   ├── LaporanController.php # Laporan rekapitulasi, export CSV, cetak PDF
│   │   ├── DosenController.php   # Daftar dosen, filter prodi, export CSV, cetak PDF
│   │   ├── AuditLogController.php # Histori aktivitas pengguna
│   │   └── PengaturanAkunController.php # Update profil pribadi & ganti password
│   └── Helpers/                  # Pustaka Bantuan
│       └── ApiClient.php         # Wrapper HTTP Request ke Backend REST API
├── assets/                       # Aset Statis Proyek
│   ├── logoumb.png               # Logo resmi universitas
│   ├── person/                   # Avatar profil contoh
│   └── university/               # Foto kampus & gedung universitas
├── public/                       # Dokumen Web Root (Entrypoint HTTP Server)
│   ├── index.php                 # Front controller, router, & konfigurasi sesi
│   ├── .htaccess                 # Konfigurasi rewrite Apache / Laragon
│   └── assets/                   # Folder aset publik (logo, gambar)
├── resources/                    # Antarmuka Pengguna (Views & Layouts)
│   └── views/
│       ├── layouts/              # Template Induk
│       │   └── app.php           # Layout utama: sidebar adaptif, topbar, profil user, & footer
│       ├── auth/
│       │   └── login.php         # Halaman login responsif dengan logo & ilustrasi kampus
│       ├── dashboard/
│       │   └── index.php         # Halaman dashboard metrik statistik
│       ├── mahasiswa/            # Halaman Modul Mahasiswa
│       │   ├── index.php         # Tabel mahasiswa, filter, pagination
│       │   ├── create.php        # Form tambah mahasiswa baru
│       │   ├── edit.php          # Form edit mahasiswa
│       │   └── detail.php        # Lembar detail data mahasiswa
│       ├── profil/               # Halaman Profil Kampus
│       │   ├── index.php         # Tampilan identitas universitas
│       │   └── edit.php          # Form ubah data universitas
│       ├── pengguna/             # Halaman Modul Pengguna
│       │   ├── index.php         # Tabel master akun pengguna
│       │   ├── create.php        # Form buat akun baru
│       │   └── edit.php          # Form ubah akun & role
│       ├── fakultas/             # Halaman Modul Fakultas
│       │   ├── index.php         # Tabel fakultas
│       │   ├── create.php        # Form tambah fakultas
│       │   └── edit.php          # Form edit fakultas
│       ├── program_studi/        # Halaman Modul Program Studi
│       │   ├── index.php         # Tabel program studi
│       │   ├── create.php        # Form tambah program studi
│       │   └── edit.php          # Form edit program studi
│       ├── laporan/              # Halaman Modul Rekapitulasi Mahasiswa
│       │   ├── index.php         # Filter status, filter prodi, ringkasan, tombol unduh
│       │   └── cetak-pdf.php     # Format cetak PDF resmi berkop & bertanda tangan
│       ├── dosen/                # Halaman Modul Daftar Dosen
│       │   ├── index.php         # Tabel dosen fakultas/prodi, status aktif, export CSV/PDF
│       │   └── cetak-pdf.php     # Format cetak PDF daftar dosen bertanda tangan
│       ├── audit_log/            # Halaman Log Aktivitas
│       │   └── index.php         # Riwayat audit log sistem
│       └── pengaturan_akun/      # Halaman Profil Akun
│           └── index.php         # Form ubah profil & ganti kata sandi
└── routes/
    └── web.php                   # Pemetaan rute URL ke Controller & Action
```

---

## 👥 Daftar Role & Fitur yang Dimiliki

Sistem antarmuka ini secara cerdas mengatur menu navigasi di sidebar serta membatasi halaman berdasarkan peran pengguna yang sedang login:

| No | Role | Navigasi Menu yang Tampil di Sidebar | Fitur & Wewenang di Frontend |
|:--:|:---|:---|:---|
| 1 | **ADMIN** | Dashboard, Profil Universitas, Data Pengguna, Fakultas, Program Studi, Data Mahasiswa, Laporan Mahasiswa, Daftar Dosen, Audit Log, Pengaturan Akun | Akses tak terbatas ke seluruh halaman dan operasi CRUD sistem. |
| 2 | **REKTORAT** | Dashboard, Laporan Mahasiswa, Daftar Dosen, Pengaturan Akun | Memantau statistik universitas, mengunduh rekapitulasi seluruh mahasiswa (CSV/PDF), serta mencetak direktori seluruh dosen. |
| 3 | **DEKANAT / ADMIN_DEKANAT** | Dashboard, Laporan Mahasiswa, Daftar Dosen, Pengaturan Akun | Memantau statistik fakultas, mengunduh rekapitulasi mahasiswa khusus fakultasnya (CSV/PDF), serta mengunduh daftar dosen fakultasnya (CSV/PDF). |
| 4 | **KAPRODI / ADMIN_PRODI** | Dashboard, Data Mahasiswa, Laporan Mahasiswa, Daftar Dosen, Pengaturan Akun | Mengelola data mahasiswa program studinya, mengunduh rekapitulasi mahasiswa prodinya (CSV/PDF), serta mengunduh daftar dosen prodinya (CSV/PDF). |
| 5 | **BAAK** | Dashboard, Data Mahasiswa, Laporan Mahasiswa, Pengaturan Akun | Mengelola seluruh data mahasiswa universitas dan mengunduh rekapitulasi akademik. |
| 6 | **DOSEN** | Dashboard, Data Mahasiswa, Pengaturan Akun | Melihat data mahasiswa bimbingan/ajar di prodinya serta mengelola akun pribadi. |
| 7 | **MAHASISWA** | Dashboard, Pengaturan Akun | Melihat ringkasan data diri mahasiswa dan memperbarui kata sandi akun. |

---

## 🔑 Daftar Akun Pengguna Bawaan (Login Credentials)

Semua akun di bawah ini telah terdaftar pada database backend dan siap digunakan untuk uji coba:

| No | Peran (Role) | Username | Password | Nama Pengguna | Lingkup / Keterangan |
|:--:|:---|:---|:---|:---|:---|
| 1 | **Administrator** | `admin` | `Admin123!` | Administrator Sistem | Akses penuh seluruh modul |
| 2 | **Rektorat** | `rektor` | `Rektor123!` | Prof. Dr. Ir. Herman Susilo, M.Sc. | Pimpinan Universitas |
| 3 | **Dekanat** | `dekan_fti` | `Dekan123!` | Dr. Hendra Gunawan, S.T., M.T. | Dekan Fakultas Teknik Informasi (FTI) |
| 4 | **Admin Dekanat** | `adm_dekanat` | `AdmDekan123!` | Staff Administrasi FTI | Tata Usaha FTI |
| 5 | **Kaprodi** | `kaprodi_ti` | `Kaprodi123!` | Dr. Ratna Sari, M.Kom. | Kaprodi S1 Teknik Informatika (FTI) |
| 6 | **Admin Prodi** | `adm_prodi_ti` | `AdmProdi123!` | Operator Prodi TI | Operator S1 Teknik Informatika |
| 7 | **BAAK** | `baak` | `Baak123!` | Petugas BAAK | Biro Administrasi Akademik |
| 8 | **Dosen** | `dosen01` | `Dosen123!` | Dr. Budi Santoso, M.Kom. | Dosen Prodi TI (FTI) |
| 9 | **Dosen** | `dosen02` | `Dosen123!` | Dr. Ir. Siti Nurhaliza, M.T. | Dosen Prodi SI (FTI) |
| 10 | **Dosen** | `dosen03` | `Dosen123!` | Ahmad Fauzi, S.Kom., M.Cs. | Dosen Prodi TI (FTI) |
| 11 | **Dosen** | `dosen04` | `Dosen123!` | Dr. Maya Indah, S.E., M.M. | Dosen Prodi Manajemen (FEB) |
| 12 | **Mahasiswa** | `mhs001` | `Mhs12345!` | Andi Pratama | Mahasiswa S1 TI (NPM: 2021101001) |

---

## 🗺 Daftar Halaman & Rute Antarmuka

| URL Path | Controller & Action | Hak Akses Role | Keterangan Halaman |
|:---|:---|:---|:---|
| `/login` | `AuthController@index` | Publik (Guest) | Form login ke sistem |
| `/logout` | `AuthController@logout` | Semua Role | Keluar dari sesi aplikasi |
| `/dashboard` | `DashboardController@index` | Semua Role | Ringkasan metrik statistik |
| `/mahasiswa` | `MahasiswaController@index` | Admin, BAAK, Prodi, Dosen | Daftar tabel data mahasiswa |
| `/mahasiswa/create` | `MahasiswaController@create` | Admin, BAAK, Prodi | Form input mahasiswa baru |
| `/mahasiswa/detail/{id}` | `MahasiswaController@detail` | Admin, BAAK, Prodi, Dosen | Detail lengkap satu mahasiswa |
| `/laporan` | `LaporanController@index` | Admin, Rektorat, Dekanat, Prodi, BAAK | Rekapitulasi mahasiswa berjenjang |
| `/laporan/export-csv` | `LaporanController@exportCsv` | Admin, Rektorat, Dekanat, Prodi, BAAK | Download CSV rekapitulasi mahasiswa |
| `/laporan/cetak-pdf` | `LaporanController@cetakPdf` | Admin, Rektorat, Dekanat, Prodi, BAAK | Cetak dokumen PDF resmi mahasiswa |
| `/dosen` | `DosenController@index` | Admin, Rektorat, Dekanat, Prodi | Direktori dosen fakultas/prodi |
| `/dosen/export-csv` | `DosenController@exportCsv` | Admin, Rektorat, Dekanat, Prodi | Download CSV data dosen |
| `/dosen/cetak-pdf` | `DosenController@cetakPdf` | Admin, Rektorat, Dekanat, Prodi | Cetak dokumen PDF resmi daftar dosen |
| `/fakultas` | `FakultasController@index` | Admin | Master data fakultas |
| `/program-studi` | `ProgramStudiController@index` | Admin | Master data program studi |
| `/pengguna` | `PenggunaController@index` | Admin | Master akun pengguna sistem |
| `/audit-log` | `AuditLogController@index` | Admin | Histori log audit aktivitas |
| `/profil-universitas` | `ProfilController@index` | Admin | Data identitas universitas |
| `/pengaturan-akun` | `PengaturanAkunController@index` | Semua Role | Ubah profil & ganti kata sandi |

---

## 💻 Panduan Menjalankan di Lingkungan Lokal

### 1. Menggunakan Laragon (Sangat Disarankan)
1. Letakkan folder proyek ini di dalam direktori `laragon/www/`:
   ```text
   d:\laragon\laragon\www\SIM_MAHASISWA\SIM_MAHASISWA_FE
   ```
2. Nyalakan service **Apache** pada panel kontrol Laragon.
3. Buka browser dan akses melalui URL:
   ```text
   http://localhost/SIM_MAHASISWA/SIM_MAHASISWA_FE/public/login
   ```

### 2. Menggunakan PHP Built-in Server
Jika tidak menggunakan Laragon atau XAMPP, Anda dapat menjalankan server internal PHP secara langsung:
```powershell
cd d:\laragon\laragon\www\SIM_MAHASISWA\SIM_MAHASISWA_FE
php -S localhost:8080 -t public
```
Kemudian buka browser pada alamat: `http://localhost:8080/login`

### 3. Konfigurasi Endpoint Backend API
Secara bawaan, file `app/Helpers/ApiClient.php` telah terhubung ke backend API Vercel:
```php
public static function getBaseUrl() {
    return 'https://sim-mhs-api.vercel.app/api/v1';
}
```
*Jika Anda ingin menghubungkannya ke API backend yang sedang berjalan di komputer lokal (port 3000), cukup sesuaikan nilai kembalian fungsi di atas:*
```php
return 'http://localhost:3000/api/v1';
```

---
*Dikembangkan untuk Sistem Informasi Mahasiswa (SIM) — Universitas Muhammadiyah Bengkulu.*
