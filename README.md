# MBG Lapor — Pelaporan dan Pemantauan Keamanan Makanan MBG

Mini Project Proyek 3 — D3 Teknik Informatika

Aplikasi web untuk membantu masyarakat melaporkan kejadian setelah konsumsi makanan program MBG, lalu memantau peninjauan dan penerusan informasinya secara terstruktur. Aplikasi tidak melakukan diagnosis dan tidak menetapkan penyebab maupun pihak yang bertanggung jawab.

## Identitas

- Nama : Muhamad Rafli Ar Rauf
- NIM : 251511047
- Kelas : 2B-D3

## Kebutuhan Environment

- PHP 8.3 atau lebih baru (dikembangkan dengan PHP 8.5)
- Ekstensi PHP: pdo_mysql, mbstring, fileinfo, openssl
- Composer
- MySQL atau MariaDB (XAMPP)
- Laravel 13

## Langkah Instalasi

1. Clone repository lalu masuk ke foldernya:

    ```bash
    git clone <URL_REPOSITORY>
    cd mbg-report
    ```

2. Install dependency:

    ```bash
    composer install
    ```

3. Salin file environment dan buat app key:

    ```bash
    copy .env.example .env
    php artisan key:generate
    ```

## Konfigurasi Database

1. Jalankan MySQL (XAMPP), lalu buat database kosong bernama `mbg_report`.
2. Sesuaikan bagian ini pada file `.env`:

    ```env
    APP_NAME="MBG Lapor"
    APP_LOCALE=id
    APP_TIMEZONE=Asia/Jakarta

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=mbg_report
    DB_USERNAME=root
    DB_PASSWORD=
    ```

## Migration dan Seeding

```bash
php artisan migrate:fresh --seed
```

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka http://127.0.0.1:8000

## Akun Demo

Password semua akun: `password`

| Role        | Email                |
| ----------- | -------------------- |
| Pelapor     | pelapor@mbg.test     |
| Pelapor 2   | pelapor2@mbg.test    |
| Pemantau    | pemantau@mbg.test    |
| Koordinator | koordinator@mbg.test |

## Catatan

- File bukti disimpan privat di `storage/app/private/evidence` dan hanya bisa dibuka lewat route yang dicek hak aksesnya.
- Seluruh data pada aplikasi adalah data dummy.
