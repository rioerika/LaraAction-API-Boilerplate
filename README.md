# Laravel API Boilerplate

Boilerplate ini dibangun untuk aplikasi backend API Laravel dengan prinsip:

- thin controller + Action Pattern
- validasi hanya melalui `FormRequest`
- response JSON seragam untuk sukses dan gagal
- autentikasi stateless memakai `Laravel Sanctum`
- RBAC memakai `spatie/laravel-permission`

## Fitur Dasar

- versioned API di `/api/v1`
- endpoint `health`, `auth`, `users`, `roles`, dan `permissions`
- role bawaan `SuperAdmin`, `Manager`, dan `User`
- permission bawaan untuk CRUD akses dan assignment role/permission
- akun `SuperAdmin` otomatis untuk environment `local` dan `testing`

## Setup Lokal

1. Sesuaikan `.env` dengan koneksi `MySQL/MariaDB`.
2. Jalankan `composer install`.
3. Jalankan `php artisan migrate --seed`.
4. Jalankan `php artisan serve`.

Default akun development:

- email: `superadmin@example.com`
- password: `password`

## Quality Gate

- format kode: `php artisan pint`
- test suite: `php artisan test`

## Catatan Testing

Suite otomatis memakai `SQLite in-memory` melalui `phpunit.xml`.

## Catatan Pest

Target awal adalah Pest, tetapi kombinasi `Laravel 13` + `PHP 8.3` pada environment ini belum kompatibel dengan rilis Pest stabil yang tersedia. Karena itu suite saat ini menggunakan `PHPUnit` sampai dependency upstream mendukung kombinasi tersebut.
