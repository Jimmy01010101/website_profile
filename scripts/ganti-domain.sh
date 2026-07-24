#!/usr/bin/env bash
# Ganti domain saat pindah lingkungan.
# Pakai: ./scripts/ganti-domain.sh http://localhost:8080 https://smkn1bengkayang.sch.id
set -e

LAMA="$1"
BARU="$2"

if [ -z "$LAMA" ] || [ -z "$BARU" ]; then
	echo "Pakai: $0 <url-lama> <url-baru>"
	exit 1
fi

WP="docker compose exec -T -u www-data wordpress wp"

echo "==> Cadangkan basis data dulu..."
docker compose exec -T db mariadb-dump -u wpuser -pwppass123 smkn1_wp > "backup-sebelum-ganti-domain-$(date +%F-%H%M).sql"

echo "==> Uji coba (belum mengubah apa pun)..."
$WP search-replace "$LAMA" "$BARU" --all-tables --dry-run

read -p "Lanjutkan mengganti? (y/n) " -n 1 -r
echo
[[ $REPLY =~ ^[Yy]$ ]] || exit 0

$WP search-replace "$LAMA" "$BARU" --all-tables
$WP option update siteurl "$BARU"
$WP option update home "$BARU"
$WP rewrite flush --hard
$WP cache flush

echo "==> Selesai. Domain sekarang: $BARU"
