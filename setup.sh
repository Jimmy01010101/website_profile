#!/usr/bin/env bash
set -e

echo "==> Menyalakan container..."
docker compose up -d
sleep 20

echo "==> Memasang WP-CLI..."
docker compose exec -T wordpress bash -c "
  curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar &&
  chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp
"

WP="docker compose exec -T -u www-data wordpress wp"

echo "==> Konfigurasi dasar..."
$WP rewrite structure '/%postname%/' --hard
$WP option update timezone_string 'Asia/Pontianak'
$WP option update date_format 'j F Y'
$WP option update blogname 'SMK Negeri 1 Bengkayang'
$WP option update default_comment_status 'closed'

echo "==> Tema & plugin..."
$WP theme install astra --activate
$WP plugin install elementor advanced-custom-fields tablepress wpforms-lite --activate

echo "==> Selesai. Buka http://localhost:8080"
