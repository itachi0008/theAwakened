#! /bin/sh -e

# Move these two into black knight
cat > .env <<EOF
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:40AFYZwCYqF2I0PzvGoO0YbDyb1qbJRrc6CnlfYOwks=
APP_URL=http://localhost

CACHE_DRIVER=file
SESSION_DRIVER=cookie
QUEUE_DRIVER=sync
EOF

php artisan config:cache

exec /start.sh
