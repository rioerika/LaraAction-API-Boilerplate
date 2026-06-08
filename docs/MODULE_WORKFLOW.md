# Workflow Pembuatan Module

Dokumen ini menjadi acuan implementasi module baru agar pola project tetap konsisten dengan `AGENTS.md`.

## Tujuan

Setiap module baru harus:

- memakai `Action Pattern`
- menjaga controller tetap tipis
- memindahkan validasi ke `FormRequest`
- memakai response JSON standar
- memasang proteksi permission sesuai kebutuhan endpoint

## Struktur Minimum

Untuk module `Product`, struktur minimum yang diharapkan:

- `app/Models/Product.php`
- `app/Actions/Products/`
- `app/Http/Requests/Api/Products/`
- `app/Http/Controllers/Api/V1/ProductController.php`
- migration baru di `database/migrations/`
- feature test baru di `tests/Feature/Api/`

## Langkah Implementasi

1. Buat migration dengan nama tabel `snake_case` dan tipe kolom yang eksplisit.
2. Buat model dengan `$fillable`, casting yang relevan, dan `declare(strict_types=1);`.
3. Buat `Store...Request` dan `Update...Request` di namespace API yang sesuai.
4. Buat action terpisah untuk `List`, `Show`, `Create`, `Update`, dan `Delete`.
5. Buat controller API tipis yang hanya:
   - menerima `FormRequest` atau model binding
   - memanggil action
   - mengembalikan `successResponse()`
6. Tambahkan route di `routes/api.php` pada prefix `/api/v1`.
7. Tambahkan middleware permission pada controller:
   - `view products`
   - `create products`
   - `update products`
   - `delete products`
8. Tambahkan permission baru ke seeder jika module memperkenalkan akses baru.
9. Tambahkan feature test minimal untuk:
   - happy path
   - validation failure
   - unauthorized/forbidden access
   - not found bila memakai route model binding
10. Perbarui `docs/ERD.md` jika ada perubahan tabel atau relasi.

## Naming Convention

- Action class: `CreateProductAction`
- Request class: `StoreProductRequest`
- Controller: `ProductController`
- Route resource: `products`
- Permission names: `view products`, `create products`, `update products`, `delete products`

## Checklist Sebelum Merge

- `composer lint`
- `composer analyse`
- `composer test`
- `composer quality`
- review endpoint baru terhadap kontrak JSON standar
- review permission middleware dan seeder akses
