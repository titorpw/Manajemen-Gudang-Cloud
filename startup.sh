#!/usr/bin/env bash
set -euo pipefail

NGINX_CONF="/etc/nginx/sites-available/default"
LARAVEL_PUBLIC_ROOT="/home/site/wwwroot/public"

echo "Configuring NGINX for Laravel public root: ${LARAVEL_PUBLIC_ROOT}"

if [ ! -f "${NGINX_CONF}" ]; then
    echo "NGINX config not found: ${NGINX_CONF}" >&2
    exit 1
fi

cp "${NGINX_CONF}" /tmp/azure-nginx-default.backup

if grep -q "root /home/site/wwwroot;" "${NGINX_CONF}"; then
    sed -i "s#root /home/site/wwwroot;#root ${LARAVEL_PUBLIC_ROOT};#" "${NGINX_CONF}"
fi

if ! grep -q "root ${LARAVEL_PUBLIC_ROOT};" "${NGINX_CONF}"; then
    sed -i "0,/root .*;/s#root .*;#root ${LARAVEL_PUBLIC_ROOT};#" "${NGINX_CONF}"
fi

if ! grep -q 'try_files \$uri \$uri/ /index.php' "${NGINX_CONF}"; then
    sed -i '/location \/ {/a\        try_files $uri $uri/ /index.php?$query_string;' "${NGINX_CONF}"
fi

nginx -t
service nginx reload
