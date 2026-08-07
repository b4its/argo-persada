# Variabel - Sesuaikan jika nama container berubah
CONTAINER_PHP=argo-php-fpm
CONTAINER_PHP_PROD=argo-prod-php-fpm
CONTAINER_NGROK=argo-prod-ngrok
CONTAINER_DB_PROD=argo-prod-db

.PHONY: perm fix-cache clear

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
	docker compose -f docker-compose.ngrok.yml up -d --build
	@echo "🚀 Menunggu tunnel ngrok aktif..."
	@echo "📡 URL domain bisa dilihat dengan: make ngrok-url"

ngrok-down:
	docker compose -f docker-compose.ngrok.yml down

ngrok-url:
	@docker logs $(CONTAINER_NGROK) 2>&1 | grep -oE "(https?://)?[a-zA-Z0-9-]+\.ngrok(-free)?\.app" | head -1
	@echo "🌐 Atau jalankan: make ngrok-logs"

ngrok-logs:
	docker compose -f docker-compose.ngrok.yml logs -f ngrok

ngrok-perm:
	@echo "🔵 Mengatur permission folder writable di dalam container produksi..."
	docker exec $(CONTAINER_PHP_PROD) chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	docker exec $(CONTAINER_PHP_PROD) chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
	@echo "✅ Selesai!"

ngrok-clear:
	docker exec $(CONTAINER_PHP_PROD) php artisan optimize:clear
	@echo "🧹 Cache produksi cleared!"

ngrok-db:
	docker exec -it $(CONTAINER_DB_PROD) mysql -u root