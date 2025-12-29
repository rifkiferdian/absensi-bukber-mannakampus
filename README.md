# Aplikasi Absensi Agenda/Acara Manna Kampus (QR Code)

![Preview](./screenshots/hero.png)

Aplikasi ini digunakan untuk mencatat absensi agenda atau acara internal Manna Kampus menggunakan QR Code. Dibangun di atas CodeIgniter 4, sistem ini membantu tim HR/EO memantau kehadiran, mengirim notifikasi, dan menyiapkan laporan acara dengan cepat.

> [Instalasi & Cara Penggunaan](#cara-penggunaan)

## Fitur Utama

- **QR Code check-in/out.** Peserta/pegawai men-scan QR di pintu masuk/keluar, sistem langsung memvalidasi dan mencatat kehadiran.
- **Manajemen agenda & acara.** Buat, ubah, dan kelola jadwal acara perusahaan Manna Kampus lengkap dengan lokasi dan waktu.
- **Dashboard admin & petugas.** Pantau kehadiran real time, status hadir/pulang, dan statistik tiap agenda.
- **Notifikasi WhatsApp (opsional).** Kirim konfirmasi kehadiran atau pengingat melalui WhatsApp setelah scan berhasil.
- **Generator & unduhan QR Code.** Cetak QR unik per peserta/undangan secara massal atau individual.
- **Impor peserta massal (CSV).** Mempercepat input daftar karyawan/undangan.
- **Penyesuaian status kehadiran.** Ubah status menjadi hadir, izin, dinas luar, atau pulang manual bila diperlukan.
- **Role pengguna.** Kelola admin, petugas, dan superadmin untuk kontrol akses.
- **Laporan PDF.** Rekap kehadiran per agenda atau periode untuk kebutuhan audit.

## Teknologi & Library

- [CodeIgniter 4](https://github.com/codeigniter4/CodeIgniter4)
- [Material Dashboard Bootstrap 4](https://www.creative-tim.com/product/material-dashboard-bs4)
- [Myth Auth Library](https://github.com/lonnieezell/myth-auth)
- [Endroid QR Code Generator](https://github.com/endroid/qr-code)
- [ZXing JS QR Code Scanner](https://github.com/zxing-js/library)
- [Fonnte](https://fonnte.com/) untuk WhatsApp API (opsional)

## Screenshots

### Tampilan Halaman QR Scanner

![QR Scanner view](./screenshots/qr-scanner.jpeg)

### Tampilan Absen Masuk dan Pulang

![QR Scanner absen](./screenshots/absen.jpg)

> #### Notifikasi via WhatsApp
>
> ![Notifikasi WA](./screenshots/notif-wa.png)

### Tampilan Login Petugas

![Login](./screenshots/login.jpeg)

### Tampilan Dashboard Petugas

![Dashboard](./screenshots/dashboard.png)

### Tampilan CRUD Data Absen (contoh dataset)

| Peserta (contoh data departemen/kelas)             |                Pembicara/Instruktur              |
| -------------------------------------------------- | :----------------------------------------------: |
| ![CRUD Absen Peserta](./screenshots/absen-siswa.png) | ![CRUD Absen Instruktur](./screenshots/absen-guru.png) |

### Tampilan Ubah Data Kehadiran

<p align="center">
  <img src="./screenshots/ubah-kehadiran.jpeg" height="320px" style="object-fit:cover" alt="Ubah Data Kehadiran" title="Ubah Data Kehadiran">
</p>

### Tampilan CRUD Data Peserta & Pembicara

| Peserta                                          |                Pembicara/Instruktur             |
| ------------------------------------------------ | :--------------------------------------------: |
| ![CRUD Data Peserta](./screenshots/data-siswa.png) | ![CRUD Data Instruktur](./screenshots/data-guru.png) |

### Tampilan CRUD Data Divisi/Kelas

![CRUD Data Divisi/Kelas](./screenshots/kelas-jurusan.png)

### Tampilan Generate QR Code dan Generate Laporan

| Generate QR                                   |                Generate Laporan                |
| --------------------------------------------- | :--------------------------------------------: |
| ![Generate QR](./screenshots/generate-qr.png) | ![Generate Laporan](./screenshots/laporan.png) |

## Cara Penggunaan

### Persyaratan

- [Composer](https://getcomposer.org/).
- PHP 8.1+ dan MySQL/MariaDB atau [XAMPP](https://www.apachefriends.org/download.html) versi 8.1+ dengan extension `intl` dan `gd` aktif.
- Perangkat dengan kamera/webcam untuk menjalankan QR scanner (bisa juga memakai kamera HP via DroidCam).

### Instalasi

- Clone/unduh source code proyek ini.

- Install dependencies yang diperlukan dengan cara menjalankan perintah berikut di terminal:

  ```shell
  composer install
  ```

- Jika belum terdapat file `.env`, rename file `.env.example` menjadi `.env`.

- Buat database `db_absensi` (atau sesuai nama di `.env`) di phpMyAdmin/MySQL.

- Jalankan migrasi database untuk membuat struktur tabel yang diperlukan. Ketikkan perintah berikut di terminal:

  ```shell
  php spark migrate --all
  ```

- Jalankan web server (contoh Apache, XAMPP, dll).
- Atau gunakan `php spark serve` (atur `baseURL` di `.env` menjadi `http://localhost:8080/` terlebih dahulu).
- Akses aplikasi di browser, lalu login sebagai superadmin:

  ```txt
  username : superadmin
  password : superadmin
  ```

- Izinkan akses kamera.

### Konfigurasi

> [!IMPORTANT]
>
> - Konfigurasi file `.env` untuk mengatur base url (terutama jika melakukan hosting), koneksi database dan pengaturan lainnya sesuai dengan lingkungan pengembangan Anda.
>
> - Untuk mengaktifkan **notifikasi WhatsApp**, pertama-tama ubah variabel `.env` berikut dari `false` menjadi `true`.
>
>   ```sh
>   # .env
>   # WA_NOTIFICATION=false # sebelum
>   WA_NOTIFICATION=true
>   ```
>
>   Lalu masukkan token WhatsApp API.
>
>   ```sh
>   # .env
>   WA_NOTIFICATION=true
>   WHATSAPP_PROVIDER=Fonnte
>   WHATSAPP_TOKEN=XXXXXXXXXXXXXXXXX # ganti dengan token anda
>   ```
>
>   _**Untuk mendapatkan token, daftar di website [fonnte](https://md.fonnte.com/new/register.php) terlebih dahulu. Lalu daftarkan device anda dan [dapatkan token Fonnte Whatsapp API](https://docs.fonnte.com/token-api-key/)**_
>
> - Untuk menyesuaikan nama perusahaan/instansi, tahun acara, dan logo sudah disediakan pengaturan (khusus superadmin).
>
> - Logo perusahaan direkomendasikan 100x100px atau 1:1 berformat PNG/JPG.
>
> - Jika ingin mengubah email, username, dan password superadmin, buka file `app\Database\Migrations\2023-08-18-000004_AddSuperadmin.php` lalu sesuaikan kode berikut:
>
>   ```php
>   // INSERT INITIAL SUPERADMIN
>   $email = 'adminsuper@gmail.com';
>   $username = 'superadmin';
>   $password = 'superadmin';
>   ```

## Kontribusi

Kontribusi dan masukan sangat dihargai. Silakan buat issue atau pull request jika menemukan bug atau ingin menambahkan fitur baru.

## Lisensi

Proyek ini dirilis di bawah lisensi MIT (lihat `LICENSE`).
