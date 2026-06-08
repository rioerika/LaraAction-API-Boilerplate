# CD Setup Template

CD GitHub Actions dinonaktifkan secara default pada boilerplate ini.

## Tujuan
- menyediakan template deploy yang aman sebagai baseline
- memisahkan quality workflow dari deployment workflow
- memastikan deployment tidak aktif otomatis sebelum environment siap

## File Template
- Workflow template: `docs/templates/cd.github-actions.yml.example`

## Aturan Penggunaan
- aktifkan CD hanya setelah CI lokal atau CI repo sudah stabil
- gunakan `workflow_dispatch` saja untuk tahap awal, jangan auto-deploy dari `push`
- pisahkan `staging` dan `production` memakai GitHub Environments
- simpan secret hanya di GitHub Secrets atau Environment Secrets
- wajib gunakan approval untuk environment `production`
- deploy dari branch/tag yang jelas, idealnya `main` untuk staging dan tag release untuk production
- jangan campur job quality dan deploy dalam satu workflow
- jalankan migration hanya bila perubahan schema memang siap dirilis
- gunakan maintenance mode untuk deploy yang berpotensi mengubah schema atau cache

## Secret Minimum
- `DEPLOY_HOST`
- `DEPLOY_PORT`
- `DEPLOY_USER`
- `DEPLOY_PATH`
- `DEPLOY_SSH_KEY`

## Cara Mengaktifkan
1. Buat folder `.github/workflows` jika belum ada.
2. Salin `docs/templates/cd.github-actions.yml.example` ke `.github/workflows/cd.yml`.
3. Buat GitHub Environments `staging` dan `production`.
4. Tambahkan secret deploy per environment.
5. Tambahkan approval rule untuk environment `production`.
6. Sesuaikan command deploy bila server Anda tidak memakai flow `git pull + composer + artisan`.

## Fungsi Setiap Sintaks Workflow

### `name`
- `name: CD`
- nama workflow yang tampil di tab Actions GitHub

### `on`
- menentukan kapan workflow boleh dijalankan
- pada template ini hanya memakai `workflow_dispatch`, artinya deploy harus dipicu manual

### `workflow_dispatch`
- trigger manual dari UI GitHub Actions
- cocok untuk tahap awal karena lebih aman dibanding auto deploy

### `inputs`
- parameter yang diminta saat workflow dijalankan
- setiap input membantu mengurangi edit file workflow untuk tiap deploy

### `inputs.environment`
- memilih target deploy seperti `staging` atau `production`
- dipakai ulang pada `environment` job dan `concurrency`

### `inputs.git_ref`
- menentukan branch, tag, atau commit yang akan di-deploy
- membuat deploy bisa reproducible dan tidak selalu harus `main`

### `inputs.run_migrations`
- boolean untuk mengontrol apakah `php artisan migrate --force` dijalankan
- berguna jika rilis tertentu tidak membawa perubahan schema

### `inputs.enable_maintenance_mode`
- boolean untuk mengontrol `php artisan down` dan `php artisan up`
- berguna jika deploy perlu melindungi user dari perubahan state sementara

### `permissions`
- membatasi izin token bawaan GitHub Actions
- `contents: read` cukup untuk checkout source code

### `concurrency`
- mencegah dua deploy ke environment yang sama berjalan bersamaan
- `group: deploy-${{ inputs.environment }}` membuat lock per environment
- `cancel-in-progress: false` menjaga deploy yang sedang berjalan tidak diputus di tengah jalan

### `jobs`
- kumpulan pekerjaan utama dalam workflow
- template ini hanya punya satu job: `deploy`

### `jobs.deploy`
- blok job untuk proses deploy aplikasi

### `name` di dalam job
- label yang tampil pada job di UI GitHub Actions
- memakai ekspresi `${{ inputs.environment }}` agar nama job mengikuti target deploy

### `runs-on`
- menentukan runner OS yang dipakai
- `ubuntu-latest` adalah default paling umum untuk workflow shell dan SSH

### `environment`
- mengikat job ke GitHub Environment
- penting untuk approval, secret environment, dan audit deploy

### `steps`
- daftar langkah yang dijalankan berurutan di dalam job

### `uses`
- memanggil action yang sudah disediakan pihak lain
- contoh:
  - `actions/checkout@v4` untuk mengambil source code
  - `webfactory/ssh-agent@v0.9.0` untuk memuat private key SSH

### `with`
- memberikan parameter ke action pada blok `uses`
- contoh `ref: ${{ inputs.git_ref }}` memberi tahu checkout branch/tag/commit mana yang dipakai

### `run`
- menjalankan shell command langsung di runner
- dipakai untuk `ssh-keyscan` dan remote deploy command

### `env`
- mendefinisikan environment variable untuk step tertentu
- memudahkan akses secret dan input tanpa mengulang ekspresi panjang

### `${{ ... }}`
- sintaks expression GitHub Actions
- dipakai untuk membaca `inputs`, `secrets`, atau context lain
- contoh:
  - `${{ inputs.environment }}`
  - `${{ secrets.DEPLOY_HOST }}`

### `secrets`
- mengambil secret dari repository atau environment GitHub
- jangan pernah hardcode host, user, atau private key di file workflow

### `ssh-keyscan`
- mengambil host key server lalu menaruhnya di `known_hosts`
- mencegah prompt interaktif saat koneksi SSH pertama

### `ssh ... <<EOF`
- membuka sesi SSH non-interaktif ke server target
- semua command di antara `<<EOF` dan `EOF` dijalankan di server tujuan

### `set -e`
- menghentikan script remote saat ada command gagal
- penting agar deploy tidak lanjut dalam state setengah jadi

### `composer install --no-dev`
- memasang dependency production saja
- `--prefer-dist` dan `--optimize-autoloader` membantu deploy lebih cepat dan autoload lebih efisien

### `php artisan optimize:clear`
- membersihkan cache Laravel lama sebelum cache baru dibangun

### `php artisan migrate --force`
- menjalankan migration di environment non-interaktif
- `--force` wajib pada production-like environment

### `php artisan config:cache`, `route:cache`, `event:cache`
- membangun cache runtime Laravel untuk performa yang lebih baik

### `php artisan down` dan `php artisan up`
- mengaktifkan dan mematikan maintenance mode
- digunakan hanya jika input `enable_maintenance_mode` bernilai `true`

## Kapan Template Ini Perlu Diubah
- jika server deploy tidak berbasis SSH
- jika Anda memakai Docker, Kubernetes, Forge, Ploi, atau Envoyer
- jika source code production tidak diambil lewat `git checkout`
- jika deployment harus melibatkan build asset frontend, queue restart, atau supervisor reload

## Catatan
- Template ini aman sebagai baseline, tetapi belum otomatis cocok untuk semua infrastruktur.
- Untuk production, saya sarankan tambah rollback strategy dan health verification setelah deploy.
