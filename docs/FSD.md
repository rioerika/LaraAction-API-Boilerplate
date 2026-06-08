# FSD Boilerplate Laravel API

## Arsitektur
- Framework: Laravel 13
- Gaya aplikasi: API-only, route versioning `/api/v1`
- Pattern utama: thin controllers + action classes di `app/Actions`
- Validasi: `FormRequest` di `app/Http/Requests/Api`
- Response contract:
  - sukses: `{ "success": true, "message": "...", "data": ... }`
  - gagal: `{ "success": false, "message": "...", "errors": ... }`

## Modul Inti
- `Health`
  - `GET /api/v1/health`
  - endpoint public untuk validasi boot aplikasi
- `Readiness`
  - `GET /api/v1/readiness`
  - endpoint public untuk validasi koneksi database dan cache
- `Auth`
  - `POST /api/v1/auth/login`
  - `POST /api/v1/auth/logout`
  - `GET /api/v1/auth/me`
- `Users`
  - CRUD user
  - assignment role ke user
- `Roles`
  - CRUD role
  - assignment permission ke role
- `Permissions`
  - CRUD permission

## Security
- Auth token: Laravel Sanctum personal access tokens
- Authorization: Spatie Permission middleware alias `role`, `permission`, `role_or_permission`
- Default policy: semua route private kecuali `health` dan `login`

## Seeder Default
- Roles:
  - `SuperAdmin`
  - `Manager`
  - `User`
- Permissions:
  - `view/create/update/delete users`
  - `assign roles`
  - `view/create/update/delete roles`
  - `assign permissions`
  - `view/create/update/delete permissions`
- Local/testing bootstrap user:
  - email `superadmin@example.com`
  - password `password`

## Workflow Pengembangan Module
1. Tambahkan migration.
2. Tambahkan model dengan `declare(strict_types=1);`.
3. Tambahkan `FormRequest` store/update.
4. Tambahkan action terpisah per operasi.
5. Tambahkan controller tipis yang hanya memanggil action.
6. Tambahkan route dan permission middleware.
7. Tambahkan feature test happy path dan edge cases.
8. Sinkronkan dokumentasi module dan ERD bila ada perubahan skema.

## Quality Gate
- Jalankan `composer lint`
- Jalankan `composer analyse`
- Jalankan `composer test`
- Jalankan `composer quality`
- Jalankan `php artisan test`
- Pastikan error API tetap memakai envelope standar
