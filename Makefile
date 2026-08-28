# Variabel - Sesuaikan jika nama container berubah
CONTAINER_PHP=argo-php-fpm
CONTAINER_PHP_PROD=argo-prod-php-fpm
CONTAINER_NGROK=argo-prod-ngrok
CONTAINER_DB_PROD=argo-prod-db

.PHONY: perm fix-cache clear ngrok-seed

# 1. Menyatukan semua urusan permission
perm:
	@echo "🟢 Mengatur kepemilikan file ke user host ($$USER)..."
	sudo chown -R $$(id -u):$$(id -g) .
	@echo "🔵 Mengatur permission folder writable di dalam container..."
	docker exec $(CONTAINER_PHP) chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	docker exec $(CONTAINER_PHP) chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	@echo "✅ Selesai! Kamu bisa hapus folder dan PHP bisa nulis file."

# 2. Bonus: Membersihkan cache Laravel yang sering bikin error di Docker
clear:
	docker exec $(CONTAINER_PHP) php artisan optimize:clear
	@echo "🧹 Cache cleared!"

# 3. DEPLOY PRODUCTION VIA NGROK (hosting, domain default ngrok)
# File terpisah dari docker-compose.yml agar tidak campur dengan lokal
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

# Kembalikan .env ke mode lokal (APP_URL http + APP_ENV local).
# Jalankan ini sebelum memakai docker compose --profile nginx/apache.
env-local:
	@if [ -f .env ]; then \
		if grep -q "^APP_URL=" .env; then sed -i "s|^APP_URL=.*|APP_URL=http://localhost:9000|" .env; else echo "APP_URL=http://localhost:9000" >> .env; fi; \
		if grep -q "^APP_ENV=" .env; then sed -i "s|^APP_ENV=.*|APP_ENV=local|" .env; else echo "APP_ENV=local" >> .env; fi; \
		if grep -q "^APP_DEBUG=" .env; then sed -i "s|^APP_DEBUG=.*|APP_DEBUG=true|" .env; else echo "APP_DEBUG=true" >> .env; fi; \
		echo "✅ .env mode lokal -> APP_URL=http://localhost:9000, APP_ENV=local"; \
	else \
		echo "⚠️  .env tidak ditemukan, jalankan 'make ngrok-install' dulu."; \
	fi
	@$(MAKE) clear

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

# Target catch-all: menyerap argumen tambahan (mis. nama seeder) agar
# "make ngrok-seed UserSeeder" tidak dianggap target tak dikenal.
%:
	@:

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

%:
	@:
