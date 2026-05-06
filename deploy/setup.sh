#!/usr/bin/env bash
# =====================================================================
# Matendo Health — one-shot Ubuntu/EC2 setup script
#
# Run AFTER cloning the repo into /var/www/matendohealth.
# Idempotent: safe to re-run. Asks confirmation before destructive steps.
#
# Usage:
#   sudo bash /var/www/matendohealth/deploy/setup.sh
# =====================================================================
set -euo pipefail

DOMAIN="matendohealth.com"
SITE_DIR="/var/www/matendohealth"
DB_NAME="matendo"
DB_USER="matendo_app"

# --- guardrails -------------------------------------------------------
if [[ $EUID -ne 0 ]]; then
    echo "This script must be run with sudo." >&2; exit 1
fi
if [[ ! -d "$SITE_DIR" ]]; then
    echo "Site directory $SITE_DIR not found. Clone the repo there first." >&2; exit 1
fi

cd "$SITE_DIR"

echo "==> 1/8  Installing system packages…"
apt-get update -y
DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apache2 mysql-server \
    php php-cli php-mysql php-mbstring php-xml php-curl php-fileinfo php-gd \
    libapache2-mod-php \
    git certbot python3-certbot-apache \
    postfix mailutils \
    unzip

echo "==> 2/8  Enabling Apache modules…"
a2enmod rewrite headers expires

echo "==> 3/8  Installing the production .htaccess…"
if [[ -f .htaccess && ! -f .htaccess.dev ]]; then
    mv .htaccess .htaccess.dev
fi
cp deploy/.htaccess.production .htaccess

echo "==> 4/8  Setting permissions…"
mkdir -p storage/logs storage/uploads
chown -R www-data:www-data "$SITE_DIR"
chmod -R 775 storage

echo "==> 5/8  Creating the database (if missing)…"
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if ! mysql -e "SELECT 1" "$DB_NAME" 2>/dev/null | grep -q 1; then
    echo "Could not connect to the new database — aborting."; exit 1
fi
# Load the schema if no tables exist yet.
TABLE_COUNT=$(mysql -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME'")
if [[ "$TABLE_COUNT" -eq 0 ]]; then
    echo "    Loading sql/schema.sql…"
    mysql "$DB_NAME" < sql/schema.sql
fi

# Prompt for the app user's password if it doesn't exist.
if ! mysql -e "SELECT User FROM mysql.user WHERE User='$DB_USER'" | grep -q "$DB_USER"; then
    echo
    read -s -p "    Set a strong password for MySQL user '$DB_USER': " DB_PASS; echo
    mysql -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
    mysql -e "GRANT SELECT, INSERT, UPDATE, DELETE ON $DB_NAME.* TO '$DB_USER'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
else
    echo "    MySQL user '$DB_USER' already exists — leaving it alone."
    DB_PASS=""
fi

echo "==> 6/8  Generating .env (if missing)…"
if [[ ! -f .env ]]; then
    APP_KEY=$(php -r "echo base64_encode(random_bytes(32));")
    APP_PEPPER=$(php -r "echo bin2hex(random_bytes(32));")
    cp .env.production.example .env
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    sed -i "s|APP_PEPPER=.*|APP_PEPPER=$APP_PEPPER|" .env
    if [[ -n "$DB_PASS" ]]; then
        # Escape pipe & ampersand so sed doesn't choke on weird passwords.
        ESC_PASS=$(printf '%s\n' "$DB_PASS" | sed 's/[\&|]/\\&/g')
        sed -i "s|DB_PASS=.*|DB_PASS=$ESC_PASS|" .env
    fi
    chown www-data:www-data .env
    chmod 640 .env
    echo "    Wrote $SITE_DIR/.env  (review it: sudo nano $SITE_DIR/.env)"
else
    echo "    .env already exists — leaving it alone."
fi

echo "==> 7/8  Installing the Apache vhost…"
cp deploy/matendohealth.conf /etc/apache2/sites-available/matendohealth.conf
a2dissite 000-default.conf 2>/dev/null || true
a2ensite matendohealth.conf
apache2ctl configtest
systemctl reload apache2

echo "==> 8/8  Setup done."
echo
echo "Next steps (manual, in this order):"
echo "  1) Verify DNS A records for $DOMAIN and www.$DOMAIN point to this server:"
echo "       dig +short $DOMAIN"
echo "  2) Issue the SSL cert:"
echo "       sudo certbot --apache -d $DOMAIN -d www.$DOMAIN \\"
echo "         --agree-tos -m info@$DOMAIN --redirect"
echo "  3) Browse: https://$DOMAIN"
echo
echo "Mail (postfix) is installed but not yet configured for outbound delivery."
echo "See DEPLOY.md §9b for SPF/DMARC setup so mail isn't flagged as spam."
