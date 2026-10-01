#!/bin/sh
set -e

# 1. Menjalankan migrasi database
echo "Menjalankan migrasi database..."
php artisan migrate --force
# Bersihkan cache agar Laravel membaca environment baru
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
# 2. Pastikan permission folder storage dan cache benar
echo "Mengatur permission..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Menjalankan Apache di foreground agar container tetap hidup
echo "Menyalakan Apache..."
exec apache2-foreground
