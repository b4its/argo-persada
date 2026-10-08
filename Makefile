# Variabel - Sesuaikan jika nama container berubah
CONTAINER_PHP=argo-php-fpm
CONTAINER_DB=argo-db
CONTAINER_NGINX=argo-nginx
CONTAINER_PMA=argo-phpmyadmin
LOCAL_PORT?=8000
PMA_PORT?=8080

CONTAINER_PHP_PROD=argo-prod-php-fpm
CONTAINER_NGROK=argo-prod-ngrok
CONTAINER_DB_PROD=argo-prod-db

.PHONY: perm fix-cache clear local local-up local-down local-restart local-ip local-env local-clear local-migrate local-seed local-logs local-perm shell php local-php local-db dev env-local phpmyadmin phpmyadmin-down pma pma-down ngrok-up ngrok-down ngrok-env ngrok-url ngrok-logs ngrok-perm ngrok-install ngrok-clear ngrok-migrate ngrok-seed ngrok-build ngrok-filament ngrok-db ngrok-php ngrok-user ngrok

# ============================================================
# 1. DEVELOPMENT LOKAL & AKSES JARINGAN (LAN / WI-FI)
# ============================================================

# Alias praktis untuk menjalankan server lokal
local: local-up
dev: local-up

# Menjalankan aplikasi secara lokal dan sinkronisasi IP LAN agar konsisten diakses device lain
local-up:
	@echo "🔍 Memeriksa port $(LOCAL_PORT)..."
	@fuser -k $(LOCAL_PORT)/tcp 2>/dev/null || true
	@$(MAKE) local-env
	@echo "🚀 Menjalankan container lokal (Nginx + PHP-FPM + MySQL)..."
	docker compose --profile nginx up -d
	@echo "⌛ Menunggu database lokal siap..."
	@for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30; do \
		if docker exec $(CONTAINER_DB) mysqladmin ping -h 127.0.0.1 -uroot --silent 2>/dev/null; then break; fi; \
		sleep 1; \
	done
	@echo "✅ Database lokal siap!"
	@echo "🔄 Menjalankan migrasi database..."
	docker exec $(CONTAINER_PHP) php artisan migrate --force
	@$(MAKE) clear
	@$(MAKE) local-ip

# Menghentikan container lokal
local-down:
	@echo "🛑 Menghentikan container lokal..."
	docker compose --profile nginx --profile phpmyadmin down
	@echo "✅ Container lokal telah dimatikan."

# Restart container lokal dengan refresh IP LAN
local-restart: local-down local-up

# Deteksi IP lokal (LAN / Wi-Fi) dan sesuaikan konfigurasi .env
local-env:
	@echo "🌐 Mendeteksi IP jaringan lokal (LAN / Wi-Fi)..."
	@ip=$$(ip route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($$i=="src") print $$(i+1)}'); \
	if [ -z "$$ip" ]; then \
		ip=$$(ip -4 addr show scope global 2>/dev/null | grep -oP '(?<=inet\s)\d+(\.\d+){3}' | grep -v '^172\.' | head -n 1); \
	fi; \
	if [ -z "$$ip" ]; then ip="localhost"; fi; \
	url="http://$$ip:$(LOCAL_PORT)"; \
	test -f .env || cp .env.example .env; \
	if grep -q "^APP_URL=" .env; then sed -i "s|^APP_URL=.*|APP_URL=$$url|" .env; else echo "APP_URL=$$url" >> .env; fi; \
	if grep -q "^APP_ENV=" .env; then sed -i "s|^APP_ENV=.*|APP_ENV=local|" .env; else echo "APP_ENV=local" >> .env; fi; \
	if grep -q "^APP_DEBUG=" .env; then sed -i "s|^APP_DEBUG=.*|APP_DEBUG=true|" .env; else echo "APP_DEBUG=true" >> .env; fi; \
	if grep -q "^DB_HOST=" .env; then sed -i "s|^DB_HOST=.*|DB_HOST=db|" .env; else echo "DB_HOST=db" >> .env; fi; \
	echo "✅ .env disinkronkan ke IP LAN: $$url (APP_ENV=local, APP_DEBUG=true, DB_HOST=db)"

# Tampilkan informasi alamat IP dan petunjuk akses untuk device lain di jaringan yang sama
local-ip:
	@ip=$$(ip route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($$i=="src") print $$(i+1)}'); \
	if [ -z "$$ip" ]; then \
		ip=$$(ip -4 addr show scope global 2>/dev/null | grep -oP '(?<=inet\s)\d+(\.\d+){3}' | grep -v '^172\.' | head -n 1); \
	fi; \
	if [ -z "$$ip" ]; then ip="localhost"; fi; \
	echo ""; \
	echo "============================================================"; \
	echo "  🌐 INFORMASI AKSES JARINGAN LOKAL (LAN / SATU WI-FI)"; \
	echo "============================================================"; \
	echo "  💻 Akses di Komputer ini (Host) : http://localhost:$(LOCAL_PORT)"; \
	echo "                                   http://$$ip:$(LOCAL_PORT)"; \
	echo "  📱 Akses di HP / Device Lain    : http://$$ip:$(LOCAL_PORT)"; \
	echo "------------------------------------------------------------"; \
	echo "  ℹ️  Pastikan perangkat lain terhubung ke Wi-Fi / LAN yang sama."; \
	echo "============================================================"; \
	echo ""

# Bersihkan cache di container lokal
local-clear: clear

# Jalankan migrasi di container lokal
local-migrate:
	docker exec $(CONTAINER_PHP) php artisan migrate --force
	@echo "✅ Migrasi database lokal selesai!"

# Jalankan database seeder di container lokal
local-seed:
	@[ -z "$(filter-out local-seed,$(MAKECMDGOALS))" ] && \
		docker exec $(CONTAINER_PHP) php artisan db:seed --force || \
		docker exec $(CONTAINER_PHP) php artisan db:seed --class=$(filter-out local-seed,$(MAKECMDGOALS)) --force
	@echo "✅ Seeder lokal selesai dijalankan!"

# Lihat realtime logs container lokal
local-logs:
	docker compose --profile nginx logs -f

# Atur permission storage, cache, public di container lokal
local-perm:
	@echo "🔵 Mengatur permission folder writable di dalam container lokal..."
	@docker exec $(CONTAINER_PHP) chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public 2>/dev/null || true
	@docker exec $(CONTAINER_PHP) chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public 2>/dev/null || true
	@echo "✅ Permission lokal siap!"

# Masuk shell bash ke container PHP lokal
shell: local-php
php: local-php
local-php:
	docker exec -it $(CONTAINER_PHP) bash

# Masuk mysql shell ke container DB lokal
local-db:
	docker exec -it $(CONTAINER_DB) mysql -u root

# Kembalikan .env ke mode lokal
env-local: local-env local-clear

# Urusan permission host
perm:
	@echo "🟢 Mengatur kepemilikan file ke user host ($$USER)..."
	sudo chown -R $$(id -u):$$(id -g) .
	@$(MAKE) local-perm
	@echo "✅ Selesai! Kamu bisa hapus folder dan PHP bisa nulis file."

# Membersihkan cache, optimasi, dan reset permission (host & container)
clear:
	@echo "🧹 Menjalankan pembersihan cache & optimasi..."
	docker exec -it $(CONTAINER_PHP) php artisan config:clear
	docker exec -it $(CONTAINER_PHP) php artisan view:clear
	docker exec -it $(CONTAINER_PHP) php artisan cache:clear
	docker exec -it $(CONTAINER_PHP) php artisan route:clear
	docker exec -it $(CONTAINER_PHP) php artisan optimize:clear
	docker exec -it $(CONTAINER_PHP) php artisan optimize
	@echo "🟢 Mengatur kepemilikan file ke user host ($$USER)..."
	@docker exec $(CONTAINER_PHP) chown -R $$(id -u):$$(id -g) /var/www/html 2>/dev/null || true
	@if sudo -n true 2>/dev/null; then sudo chown -R $$USER:$$USER .; elif [ -z "$$ANTIGRAVITY_AGENT" ] && [ -t 0 ]; then sudo chown -R $$USER:$$USER .; else sudo -n chown -R $$USER:$$USER . 2>/dev/null || true; fi
	@echo "🔵 Mengatur permission folder di dalam container..."
	docker exec -it $(CONTAINER_PHP) chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
	docker exec -it $(CONTAINER_PHP) chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
	docker exec -it $(CONTAINER_PHP) chown -R www-data:www-data /var/www/html/public/
	docker exec -it $(CONTAINER_PHP) chmod -R 775 /var/www/html/public/
	@echo "✅ Cache cleared & permission selesai!"

# ============================================================
# PHPMYADMIN (Database GUI)
# ============================================================
pma: phpmyadmin
pma-down: phpmyadmin-down

# Menjalankan phpMyAdmin di web browser
phpmyadmin:
	@echo "🚀 Menjalankan phpMyAdmin container..."
	@docker compose --profile phpmyadmin up -d
	@ip=$$(ip route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($$i=="src") print $$(i+1)}'); \
	if [ -z "$$ip" ]; then \
		ip=$$(ip -4 addr show scope global 2>/dev/null | grep -oP '(?<=inet\s)\d+(\.\d+){3}' | grep -v '^172\.' | head -n 1); \
	fi; \
	if [ -z "$$ip" ]; then ip="localhost"; fi; \
	echo ""; \
	echo "============================================================"; \
	echo "  🐬 PHPMYADMIN SIAP DIGUNAKAN DI WEB BROWSER"; \
	echo "============================================================"; \
	echo "  💻 Akses di Komputer ini (Host) : http://localhost:$(PMA_PORT)"; \
	echo "                                   http://$$ip:$(PMA_PORT)"; \
	echo "  📱 Akses di HP / Device Lain    : http://$$ip:$(PMA_PORT)"; \
	echo "------------------------------------------------------------"; \
	echo "  🔑 User: root  |  Password: (kosong / tanpa password)"; \
	echo "  🗄️ Database: argopersada"; \
	echo "============================================================"; \
	echo ""

# Menghentikan phpMyAdmin container
phpmyadmin-down:
	@echo "🛑 Menghentikan phpMyAdmin container..."
	docker compose --profile phpmyadmin stop phpmyadmin
	@echo "✅ phpMyAdmin telah dinonaktifkan."

# ============================================================
# 2. DEPLOY PRODUCTION VIA NGROK (hosting, domain default ngrok)
# ============================================================
ngrok-up:
	@echo "🔍 Membersihkan semua container lama yang mungkin masih stuck..."
	docker compose -f docker-compose.ngrok.yml down --remove-orphans 2>/dev/null || true
	@echo ""
	@echo "🔍 Mengecek dan mematikan proses yang menggunakan port 9000 atau 4040..."
	lsof -t -i:9000 2>/dev/null | xargs -r kill -9 || true
	lsof -t -i:4040 2>/dev/null | xargs -r kill -9 || true
	fuser -k 9000/tcp 2>/dev/null || true
	fuser -k 4040/tcp 2>/dev/null || true
	sleep 2
	@echo "✅ Semua port sudah bersih!"
	docker compose -f docker-compose.ngrok.yml up -d --build --force-recreate
	@echo "🚀 Menunggu tunnel ngrok aktif..."
	@$(MAKE) ngrok-env
	@$(MAKE) ngrok-migrate
	@$(MAKE) ngrok-clear
	@$(MAKE) ngrok-url

ngrok-env:
	@echo "🔄 Menyesuaikan APP_URL dengan domain ngrok aktif..."
	@url=""; \
	for i in 1 2 3 4 5 6 7 8 9 10 11 12; do \
		url="$$(curl -s --max-time 2 http://localhost:4040/api/tunnels 2>/dev/null | grep -oE '"public_url":"[^"]+"' | head -1 | cut -d'"' -f4)"; \
		if [ -n "$$url" ]; then break; fi; \
		sleep 1; \
	done; \
	if [ -z "$$url" ]; then url="$$(docker logs $(CONTAINER_NGROK) 2>&1 | grep -oE "https://[a-zA-Z0-9-]+\.ngrok[a-z0-9.-]+\.[a-z]+" | tail -1)"; fi; \
	if [ -z "$$url" ]; then echo "❌ Domain ngrok kosong setelah 12x coba. Cek: make ngrok-logs (pastikan container argo-prod-ngrok jalan & port 4040 tidak dipakai proses lain)."; exit 1; fi; \
	if [ -f .env ]; then \
		if grep -q "^APP_URL=" .env; then sed -i "s|^APP_URL=.*|APP_URL=$$url|" .env; else echo "APP_URL=$$url" >> .env; fi; \
		if grep -q "^APP_ENV=" .env; then sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env; else echo "APP_ENV=production" >> .env; fi; \
		if grep -q "^APP_DEBUG=" .env; then sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env; else echo "APP_DEBUG=false" >> .env; fi; \
		echo "✅ .env -> APP_URL=$$url, APP_ENV=production, APP_DEBUG=false"; \
	else \
		echo "⚠️  .env tidak ditemukan, jalankan 'make ngrok-install' dulu. URL aktif: $$url"; \
	fi
	@echo "ℹ️  Lanjutkan dengan: make ngrok-migrate lalu make ngrok-clear"

ngrok-down:
	docker compose -f docker-compose.ngrok.yml down

ngrok-url:
	@echo "📡 URL aktif:"
	@curl -s --max-time 3 http://localhost:4040/api/tunnels 2>/dev/null | grep -oE '"public_url":"[^"]+"' | head -1 | cut -d'"' -f4 || \
		docker logs $(CONTAINER_NGROK) 2>&1 | grep -oE "https://[a-zA-Z0-9-]+\.ngrok[a-z0-9.-]+\.[a-z]+" | tail -1
	@echo "🌐 Atau lihat log: make ngrok-logs"

ngrok-logs:
	docker compose -f docker-compose.ngrok.yml logs -f ngrok

ngrok-perm:
	@echo "🔵 Mengatur permission folder writable di dalam container produksi..."
	docker exec $(CONTAINER_PHP_PROD) chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	docker exec $(CONTAINER_PHP_PROD) chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	@echo "✅ Selesai!"

ngrok-install:
	@echo "📦 Menyiapkan .env (jika belum ada) & menginstall dependency composer di container produksi..."
	@test -f .env || cp .env.example .env
	@grep -q "DB_HOST=db" .env || sed -i "s/^DB_HOST=.*/DB_HOST=db/" .env
	docker exec $(CONTAINER_PHP_PROD) composer install
	docker exec $(CONTAINER_PHP_PROD) php artisan key:generate
	@echo "✅ Dependency terinstall!"

ngrok-clear:
	@echo "⌛ Menunggu database produksi siap..."
	@for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30; do \
		if docker exec $(CONTAINER_DB_PROD) mysqladmin ping -h 127.0.0.1 -uroot --silent 2>/dev/null; then break; fi; \
		sleep 1; \
	done
	@echo "🧹 Menghapus cache produksi..."
	docker exec $(CONTAINER_PHP_PROD) php artisan config:clear
	docker exec $(CONTAINER_PHP_PROD) php artisan view:clear
	@docker exec $(CONTAINER_PHP_PROD) php artisan cache:clear || echo "⚠️ cache:clear dilewati (tabel cache belum ada - jalankan make ngrok-migrate dulu)"
	docker exec $(CONTAINER_PHP_PROD) php artisan route:clear
	@docker exec $(CONTAINER_PHP_PROD) php artisan optimize:clear || echo "⚠️ optimize:clear dilewati (tabel cache belum ada - jalankan make ngrok-migrate dulu)"
	@docker exec $(CONTAINER_PHP_PROD) php artisan optimize || echo "⚠️ optimize dilewati (tabel cache belum ada - jalankan make ngrok-migrate dulu)"
	@docker exec $(CONTAINER_PHP_PROD) php artisan optimize:clear || echo "⚠️ optimize:clear dilewati (tabel cache belum ada - jalankan make ngrok-migrate dulu)"
	@echo "🧹 Cache produksi cleared!"

ngrok-migrate:
	docker exec $(CONTAINER_PHP_PROD) php artisan migrate --force
	@echo "✅ Migrasi database produksi selesai!"

ngrok-seed:
	@[ -z "$(filter-out ngrok-seed,$(MAKECMDGOALS))" ] && \
		docker exec $(CONTAINER_PHP_PROD) php artisan db:seed --force || \
		docker exec $(CONTAINER_PHP_PROD) php artisan db:seed --class=$(filter-out ngrok-seed,$(MAKECMDGOALS)) --force
	@echo "✅ Seeder produksi selesai dijalankan!"

ngrok-build:
	@echo "📦 Compile asset Vite (public/build/manifest.json) di container produksi..."
	docker exec $(CONTAINER_PHP_PROD) npm install
	docker exec $(CONTAINER_PHP_PROD) npm run build
	@echo "✅ Asset selesai dibuild!"

ngrok-filament:
	@echo "🧩 Publish asset Filament ke public/vendor/filament..."
	docker exec $(CONTAINER_PHP_PROD) php artisan filament:assets
	docker exec $(CONTAINER_PHP_PROD) php artisan filament:optimize
	@echo "✅ Asset Filament siap!"

ngrok-db:
	docker exec -it $(CONTAINER_DB_PROD) mysql -u root

ngrok-php:
	docker exec -it $(CONTAINER_PHP_PROD) bash

ngrok-user:
	docker exec -it $(CONTAINER_PHP_PROD) php artisan make:filament-user

# Start ngrok tunnel secara manual (untuk debugging atau run terpisah)
ngrok:
	@./ngrok-start.sh

# Target catch-all: menyerap argumen tambahan (mis. nama seeder) agar
# "make ngrok-seed UserSeeder" atau "make local-seed UserSeeder" tidak dianggap target tak dikenal.
%:
	@:
