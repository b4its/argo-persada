# Variabel - Sesuaikan jika nama container berubah
CONTAINER_PHP=argo-php-fpm

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