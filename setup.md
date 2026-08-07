# Setup Docker - Lokal & Deploy via Ngrok

## Prasyarat

- Docker + Docker Compose (versi terbaru)
- `make` (opsional, untuk shortcut Makefile)
- Akun ngrok + authtoken (dari dashboard ngrok: https://dashboard.ngrok.com/get-started/your-authtoken)

## Struktur File

| File | Kegunaan |
|------|----------|
| `docker-compose.yml` | Setup lokal (Apache atau Nginx) |
| `docker-compose.ngrok.yml` | Setup produksi/hosting via ngrok (DB + Nginx + PHP-FPM + Ngrok) |
| `docker-apache/Dockerfile` | Image PHP-FPM untuk profil apache |
| `docker-nginx/Dockerfile` | Image PHP-FPM untuk profil nginx (ada Node.js untuk Vite) |
| `docker-nginx/default.conf` | Konfigurasi Nginx (root ke `public/`) |
| `Makefile` | Shortcut permission, clear cache, dan deploy ngrok |

> Lokal dan produksi TIDAK dicampur. Masing-masing punya compose file sendiri,
> jadi container produksi tidak mengganggu development lokal.

---

## 1. Setup Lokal (Development)

### 1.1. Buat `.env`

```bash
cp .env.example .env
php artisan key:generate   # atau lewat container setelah up
```

Sesuaikan konfigurasi database di `.env` (untuk Docker pakai host `db`):

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=argopersada
DB_USERNAME=root
DB_PASSWORD=
```

### 1.2. Jalankan (pilih salah satu)

```bash
# Pakai Nginx + PHP-FPM (disarankan)
docker compose --profile nginx up -d --build

# atau pakai Apache
docker compose --profile apache up -d --build
```

Akses di `http://localhost:8000`.

### 1.3. Setup permission & cache

```bash
make perm     # kepemilikan file + folder writable (storage, bootstrap/cache, public)
make clear    # optimize:clear (jalankan setiap ada error cache)
```

### 1.4. Migrasi database

```bash
docker exec -it argo-php-fpm php artisan migrate --seed
```

### 1.5. Berhenti

```bash
docker compose down          # berhenti + hapus container (data mysql tersimpan di volume)
docker compose down -v       # reset total, hapus juga volume mysql
```

---

## 2. Deploy Produksi via Ngrok (Hosting)

Menampilkan aplikasi ke internet memakai tunnel ngrok dengan **domain default ngrok**
(contoh: `xxxx-xxxx.ngrok-free.app`), tanpa perlu port-forwarding.

### 2.1. Siapkan authtoken

Ganti `NGROK_AUTHTOKEN` di `docker-compose.ngrok.yml` dengan token kamu:

```yaml
ngrok:
  environment:
    - NGROK_AUTHTOKEN=cr_3HZBtcWCX7No6tysdEqgKOzmz1L   # ganti dengan token kamu
```

> **Penting:** jangan commit token ke git. Sebaiknya pakai `.env`:
>
> ```bash
> echo "NGROK_AUTHTOKEN=cr_3HZBtcWCX7No6tysdEqgKOzmz1L" >> .env
> ```
>
> lalu di `docker-compose.ngrok.yml` ubah menjadi `${NGROK_AUTHTOKEN}`.

### 2.2. Build & jalankan

```bash
make ngrok-up
# atau manual:
docker compose -f docker-compose.ngrok.yml up -d --build
```

Service yang jalan: `db` (mysql), `php-fpm`, `nginx`, `ngrok`.

### 2.3. Ambil URL domain

```bash
make ngrok-url
# atau
docker logs argo-prod-ngrok
```

Log akan menampilkan URL tunnel, contoh: `Forwarding https://abc-123.ngrok-free.app -> http://nginx:80`.

### 2.4. Sesuaikan `.env` produksi

`make ngrok-up` otomatis menyinkronkan `APP_URL` ke domain ngrok aktif
dan clear cache (lewat `ngrok-env`). Kalau domain berubah (restart),
jalankan manual:

```bash
make ngrok-url      # cek domain aktif
make ngrok-env      # update APP_URL di .env + clear cache
```

Setelan yang sudah benar saat produksi:

```
APP_URL=<domain ngrok aktif - diatur otomatis>
APP_ENV=production
APP_DEBUG=false
```

> **Penting:** karena memakai domain default ngrok, URL berubah setiap
> container ngrok restart. Selalu jalankan `make ngrok-env` setelah restart.
> Kalau mau URL tetap, reserve custom domain di dashboard ngrok lalu tambahkan
> argumen `--domain=namadomain.ngrok-free.app` di `command` service ngrok.

### 2.5. Install dependency & monitoring

```bash
make ngrok-install   # composer install + key:generate (vendor/ tidak ada)
make ngrok-perm      # permission folder (storage, bootstrap/cache, public)
make ngrok-build     # compile asset Vite → public/build/manifest.json (wajib buat production)
make ngrok-filament  # publish asset Filament → public/vendor/filament (CSS/JS panel)
make ngrok-migrate   # jalankan migrasi database produksi (sekali sebelum dipakai)
make ngrok-seed      # jalankan semua seeder (db:seed)
make ngrok-seed UserSeeder   # jalankan seeder spesifik (db:seed --class=UserSeeder)
make ngrok-clear     # clear cache produksi
make ngrok-logs      # ikuti log tunnel ngrok (CTRL+C untuk keluar)
make ngrok-db        # masuk terminal mysql produksi
make ngrok-php       # masuk bash container php-fpm produksi
make ngrok-user      # buat user Filament (interaktif: nama, email, password)
make ngrok-down      # hentikan produksi
docker compose -f docker-compose.ngrok.yml ps   # cek status container
```

### 2.6. Import/Export database produksi

Container DB produksi bernama `argo-prod-db`:

```bash
# import
docker exec -i argo-prod-db mysql -u root argopersada < file.sql

# export
docker exec argo-prod-db mysqldump -u root argopersada > backup.sql
```

---

## 3. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| 403 / file tidak ketemu | `make perm` (lokal) / `make ngrok-perm` (produksi) lalu clear cache |
| Port 8000 sudah dipakai | ganti port di compose file (`"8000:80"` → `"8001:80"`) |
| Ngrok `ERR_NGROK_401` / authtoken invalid | cek token di dashboard ngrok, update `NGROK_AUTHTOKEN`, `make ngrok-down && make ngrok-up` |
| Cache lama tidak update | `make clear` (lokal) / `make ngrok-clear` (produksi) |
| Permission denied di storage | `make perm` (lokal) / `make ngrok-perm` (produksi) |
| Conflict nama container (`/argo-*` already in use) | nama container produksi sudah di-prefix `-prod`, jangan jalankan lokal & produksi barengan, atau bongkar container lama dulu |
| Data (sakit) tiba-tiba hilang dari DB | Volume dipisah: lokal = `mysql_data_local`, produksi = `mysql_data_prod`. Sebelumnya keduanya memakai volume bersama `mysql_data` sehingga `docker compose ... down -v` menghapus data kedua-duanya. Terapkan kebiasaan: export/backup dulu sebelum menjalankan perintah berakhiran `-v`. |
| Asset Filament/CSS/JS tidak muncul padahal HTTP 200 (mixed content) | ngrok selalu HTTPS ke luar, tapi nginx di dalam menerima HTTP biasa. Laravel jadi generate URL asset `http://...` dan diblokir browser karena halaman `https://...` (mixed content). Sudah diperbaiki lewat `bootstrap/app.php` (`trustProxies`) + `AppServiceProvider` (`URL::forceScheme('https')` saat `APP_URL` https). Kalau masih terjadi, jalankan `make ngrok-clear` lagi setelah pull kode terbaru |
