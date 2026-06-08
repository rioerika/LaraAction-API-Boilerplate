# CI Setup Template

CI/CD GitHub Actions dinonaktifkan secara default pada boilerplate ini.

## Tujuan
- menjaga repo tetap ringan saat tahap awal development
- tetap menyediakan standar workflow yang siap dipakai ulang
- memastikan quality gate lokal tetap sama dengan quality gate CI nanti

## Template Standar
- Workflow template: `docs/templates/ci.github-actions.yml.example`
- Local quality gate yang harus tetap dipakai:
  - `composer lint`
  - `composer analyse`
  - `composer test`
  - `composer quality`

## Aturan Penggunaan
- aktifkan CI sebelum CD, bukan sebaliknya
- jadikan CI sebagai quality gate minimum untuk setiap push dan pull request
- jangan campur step deploy ke dalam workflow CI
- gunakan workflow CI untuk verifikasi code quality, bukan untuk provisioning server
- pertahankan parity antara local check dan CI check
- jika command lokal berubah, update template CI di file example yang sama
- jaga CI tetap cepat; tambahkan step baru hanya jika memang memberi sinyal kualitas yang jelas
- prioritaskan failure yang deterministik, hindari step yang bergantung pada service eksternal bila tidak perlu
- jika nanti menambah matrix PHP version, mulai dari satu versi stabil terlebih dahulu

## Cara Mengaktifkan GitHub Actions
1. Buat folder `.github/workflows` jika belum ada.
2. Salin `docs/templates/ci.github-actions.yml.example` ke `.github/workflows/ci.yml`.
3. Sesuaikan branch trigger, versi PHP, dan extension bila stack berubah.
4. Commit file workflow tersebut.

## Standar Minimum Workflow
- gunakan `ubuntu-latest`
- gunakan `shivammathur/setup-php`
- generate `.env` dan application key sebelum test
- jalankan minimal:
  - `composer lint`
  - `composer analyse`
  - `composer test`

## Fungsi Setiap Sintaks Workflow

### `name`
- `name: CI`
- nama workflow yang tampil di tab Actions GitHub

### `on`
- menentukan kapan workflow dijalankan
- pada template ini CI berjalan saat ada `push` dan `pull_request`

### `push`
- menjalankan workflow ketika ada commit yang didorong ke repository
- berguna untuk menjaga branch tetap sehat walau perubahan tidak lewat pull request

### `branches`
- memfilter branch yang memicu workflow
- template memakai `**` agar semua branch ikut diverifikasi

### `pull_request`
- menjalankan workflow saat ada aktivitas pada pull request
- penting untuk memastikan kode lolos review gate sebelum merge

### `jobs`
- kumpulan job utama di dalam workflow
- template ini hanya memakai satu job: `quality`

### `jobs.quality`
- job yang bertanggung jawab menjalankan verifikasi kualitas source code

### `runs-on`
- menentukan OS runner yang dipakai
- `ubuntu-latest` dipilih karena paling umum dan stabil untuk workflow PHP CLI

### `steps`
- daftar langkah yang dieksekusi berurutan dalam satu job

### `uses`
- memanggil reusable GitHub Action dari pihak lain
- pada template ini dipakai untuk:
  - `actions/checkout@v4`
  - `shivammathur/setup-php@v2`

### `actions/checkout@v4`
- mengambil isi repository ke runner agar source code tersedia untuk step berikutnya

### `shivammathur/setup-php@v2`
- menyiapkan runtime PHP pada runner GitHub Actions
- ini adalah action standar yang umum dipakai di ekosistem PHP

### `with`
- memberikan parameter ke action pada blok `uses`
- dipakai untuk mengatur versi PHP, coverage, dan extension

### `php-version`
- menentukan versi PHP yang dipakai runner
- harus selaras dengan requirement utama project di `composer.json`

### `coverage`
- mengatur kebutuhan extension coverage
- `none` dipakai agar setup lebih cepat karena workflow ini belum menjalankan coverage report

### `extensions`
- menentukan extension PHP tambahan yang perlu diaktifkan
- pada template ini `mbstring`, `pdo_sqlite`, dan `sqlite3` dibutuhkan untuk analisis dan test suite

### `run`
- menjalankan shell command langsung di runner
- semua step quality utama di template memakai `run`

### `cp .env.example .env`
- membuat file environment sementara di runner
- dibutuhkan agar Laravel bisa bootstrap dengan benar

### `php artisan key:generate`
- menghasilkan `APP_KEY` untuk environment CI
- membantu mencegah error bootstrap framework saat test

### `composer install --no-interaction --prefer-dist --optimize-autoloader`
- memasang dependency project di runner
- `--no-interaction` mencegah prompt interaktif
- `--prefer-dist` mempercepat install dari archive
- `--optimize-autoloader` membantu autoload lebih efisien

### `composer lint`
- menjalankan pemeriksaan format code tanpa mengubah file
- di project ini memetakan ke `vendor/bin/pint --test`

### `composer analyse`
- menjalankan static analysis via PHPStan/Larastan
- dipakai untuk menangkap bug tipe dan kontrak code sebelum runtime

### `composer test`
- menjalankan suite test project melalui script Composer
- di repo ini juga membersihkan config cache sebelum test dijalankan

## Kapan Template Ini Perlu Diubah
- jika versi PHP project berubah
- jika test suite butuh extension tambahan
- jika quality gate bertambah, misalnya security scan atau coverage report
- jika branch strategy berubah dan tidak semua branch perlu memicu CI
- jika project nanti butuh matrix test lintas versi PHP atau database

## Catatan
- Template ini hanya untuk quality gate CI.
- Untuk deployment gunakan template terpisah di `docs/templates/cd.github-actions.yml.example`.
- Panduan CD ada di `docs/CD_SETUP.md`.
