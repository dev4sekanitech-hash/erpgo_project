#!/bin/bash

PORT="${PORT:-10000}"
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/:10000>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Run synchronously so the sessions table exists before Apache serves requests.
# timeout prevents hanging if the DB isn't reachable yet.
php artisan config:clear 2>&1 || true
php artisan session:table 2>/dev/null || true
timeout 60 php artisan migrate --force 2>&1 || echo "[WARNING] Migration failed or timed out"
php artisan modules:sync 2>&1 || echo "[WARNING] modules:sync failed"
touch /var/www/html/storage/installed
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage 2>/dev/null || true

cat /etc/apache2/ports.conf
echo "Starting Apache on port $PORT"
exec apache2-foreground
