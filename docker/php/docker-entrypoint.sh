#!/bin/sh
set -e

APP_DIR=/var/www/html
DEV_UID=${DEV_UID:-1000}
DEV_GID=${DEV_GID:-1000}
DEV_USER=devuser

# create runtime dirs and files
mkdir -p "$APP_DIR/storage" "$APP_DIR/storage/logs" "$APP_DIR/bootstrap/cache" "$APP_DIR/database"
touch "$APP_DIR/storage/logs/laravel.log" "$APP_DIR/database/database.sqlite" || true

# run chown/chmod as root (entrypoint runs as root)
chown -R "${DEV_UID}:${DEV_GID}" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database" || true
chmod -R u+rwX,g+rwX "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/database" || true

# drop privileges to dev user and run provided command
exec "$@"
