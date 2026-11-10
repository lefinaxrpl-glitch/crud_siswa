# 📚 CRUD Data Siswa

Aplikasi CRUD Data Siswa berbasis web yang dibuat menggunakan PHP Native dan MySQL. Aplikasi ini digunakan untuk mengelola data siswa di sekolah dengan sistem login berdasarkan hak akses Admin dan Petugas.

## ✨ Fitur Aplikasi

- Login dan Logout
- Dashboard Admin dan Petugas
- Mengelola Data Siswa (Tambah, Tampil, Edit, Hapus)
- Validasi NIS tepat 10 digit angka
- Pencarian Data Siswa
- Filter Data Siswa
- Pagination
- Manajemen Data Petugas
- Laporan Data Siswa
- Cetak atau Simpan Laporan sebagai PDF
- Riwayat Aktivitas Pengguna
- Ganti Password
- Hak Akses Admin dan Petugas

## 🛠️ Teknologi yang Digunakan

- PHP Native
- MySQL / MariaDB
- PDO
- HTML
- CSS
- JavaScript
- XAMPP
- phpMyAdmin

## 📂 Struktur Folder

```text
crud_siswa/
│
├── auth.php
├── koneksi.php
├── login.php
├── logout.php
├── index.php
├── tambah.php
├── edit.php
├── hapus.php
├── dashboard_admin.php
├── dashboard_petugas.php
├── data_petugas.php
├── laporan.php
├── riwayat.php
├── ganti_password.php
├── log.php
└── style.css
```

## ⚙️ Cara Instalasi

1. Download atau clone repository ini.
2. Pindahkan folder project ke:

   `C:\xampp\htdocs\`

3. Jalankan Apache dan MySQL melalui XAMPP.
4. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
5. Buat database dengan nama `db_sekolah`.
6. Import file database SQL jika tersedia.
7. Sesuaikan konfigurasi database pada `koneksi.php`.
8. Buka aplikasi melalui:

   `http://localhost/crud_siswa/login.php`

## 👤 Hak Akses Pengguna

| Role | Hak Akses |
|---|---|
| Admin | Mengelola data siswa, petugas, laporan, dan riwayat aktivitas |
| Petugas | Melihat dan mengelola data siswa serta laporan |

## 🎯 Tujuan Pembuatan

Project ini dibuat sebagai tugas pembelajaran untuk menerapkan konsep CRUD (Create, Read, Update, Delete), koneksi database menggunakan PDO, autentikasi login, pengelolaan hak akses, dan pengembangan aplikasi web menggunakan PHP Native.

## 👩‍💻 Pengembang

**Nama:** Lefina Restiana Wahyu Saputri  
**Kelas:** XII RPL 2  
**Sekolah:** SMK PGRI 2 Ponorogo

---

© 2026 CRUD Data Siswa
