#!/usr/bin/env bash
# =====================================================================
# Matendo Health — SAFE Ubuntu/EC2 setup script for shared servers.
#
# This version assumes the EC2 instance already runs other projects
# (Laravel, Flutter web builds, HTML sites) and avoids any change that
# could disrupt them:
#   - Does NOT install postfix (use existing mail or install manually)
#   - Does NOT disable 000-default.conf
#   - Does NOT touch other databases
#   - Refuses to run if a vhost already serves matendohealth.com
#
# Run:
#   sudo bash /var/www/Matendo/deploy/setup.sh
# =====================================================================
set -euo pipefail

DOMAIN="matendohealth.com"
SITE_DIR="/var/www/Matendo"
DB_NAME="matendodb"
DB_USER="root"   # the app uses root locally; least-privilege user recommended for prod

# --- guardrails -------------------------------------------------------
if [[ $EUID -ne 0 ]]; then
    echo "This script must be run with sudo." >&2; exit 1
fi
if [[ ! -d "$SITE_DIR" ]]; then
    echo "Site directory $SITE_DIR not found. Clone the repo there first." >&2; exit 1
fi

cd "$SITE_DIR"

# --- pre-flight: refuse to clobber existing config -------------------
echo "==> Pre-flight: checking for conflicts with existing projects…"

if [[ -f /etc/apache2/sites-enabled/matendohealth.conf ]] || \
   sudo grep -rqsi "ServerName\s\+matendohealth\.com" /etc/apache2/sites-enabled/ 2>/dev/null; then
    echo "    ⚠  An Apache vhost for $DOMAIN already exists. Skipping vhost install."
    INSTALL_VHOST=0
else
    INSTALL_VHOST=1
fi

if mysql -e "SHOW DATABASES LIKE '$DB_NAME'" 2>/dev/null | grep -q "$DB_NAME"; then
    echo "    ℹ  Database '$DB_NAME' already exists — will not recreate or wipe it."
    DB_EXISTS=1
else
    DB_EXISTS=0
fi

# --- 1/6 packages (no postfix; nothing destructive) ------------------
echo "==> 1/6  Installing system packages (idempotent)…"
apt-get update -y

# Detect existing PHP major.minor version to avoid upgrading the user's
# Laravel/etc. installs. If none is present, fall back to the meta package.
PHP_BIN=$(command -v php || true)
if [[ -n "$PHP_BIN" ]]; then
    PHP_VER=$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
    echo "    Detected PHP $PHP_VER — installing matching extensions only."
    PHP_PKGS="php${PHP_VER}-cli php${PHP_VER}-mysql php${PHP_VER}-mbstring php${PHP_VER}-xml php${PHP_VER}-curl php${PHP_VER}-gd libapache2-mod-php${PHP_VER}"
else
    echo "    No PHP detected — installing the default meta package."
    PHP_PKGS="php php-cli php-mysql php-mbstring php-xml php-curl php-gd libapache2-mod-php"
fi

# fileinfo is built into php-common (auto-pulled), so we don't list it.
DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    apache2 mysql-server \
    $PHP_PKGS \
    git certbot python3-certbot-apache

# --- 2/6 Apache modules ----------------------------------------------
echo "==> 2/6  Enabling Apache modules (mod_rewrite, headers, expires)…"
a2enmod rewrite headers expires >/dev/null

# --- 3/6 .htaccess (only inside our project folder) ------------------
echo "==> 3/6  Installing the production .htaccess…"
if [[ -f .htaccess && ! -f .htaccess.dev ]]; then
    cp .htaccess .htaccess.dev   # keep the original around as a backup
fi
cp deploy/.htaccess.production .htaccess

# --- 4/6 permissions (only inside our project folder) ----------------
echo "==> 4/6  Setting permissions on $SITE_DIR…"
mkdir -p storage/logs storage/uploads
chown -R www-data:www-data "$SITE_DIR"
chmod -R 775 storage

# --- 5/6 database (additive only) ------------------------------------
echo "==> 5/6  Database setup…"
if [[ "$DB_EXISTS" -eq 0 ]]; then
    mysql -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    echo "    Created database '$DB_NAME'."
fi

TABLE_COUNT=$(mysql -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME'")
if [[ "$TABLE_COUNT" -eq 0 ]]; then
    echo "    Loading sql/schema.sql into '$DB_NAME'…"
    mysql "$DB_NAME" < sql/schema.sql
else
    echo "    '$DB_NAME' already has $TABLE_COUNT tables — leaving them alone."
fi

# --- 6/6 .env (only created if missing) ------------------------------
echo "==> 6/6  Generating .env (only if missing)…"
if [[ ! -f .env ]]; then
    APP_KEY=$(php -r "echo base64_encode(random_bytes(32));")
    APP_PEPPER=$(php -r "echo bin2hex(random_bytes(32));")
    cp .env.production.example .env
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    sed -i "s|APP_PEPPER=.*|APP_PEPPER=$APP_PEPPER|" .env
    sed -i "s|DB_NAME=.*|DB_NAME=$DB_NAME|" .env
    sed -i "s|DB_USER=.*|DB_USER=$DB_USER|" .env
    chown www-data:www-data .env
    chmod 640 .env
    echo "    Wrote $SITE_DIR/.env"
    echo "    ⚠  DB_PASS is blank in .env — if MySQL root has a password, edit .env now:"
    echo "       sudo nano $SITE_DIR/.env"
else
    echo "    .env already exists — leaving it alone."
fi

# --- vhost (only if not already there) -------------------------------
if [[ "$INSTALL_VHOST" -eq 1 ]]; then
    echo "==> Installing the Apache vhost (matendohealth.conf)…"
    cp deploy/matendohealth.conf /etc/apache2/sites-available/matendohealth.conf
    a2ensite matendohealth.conf >/dev/null
    apache2ctl configtest
    systemctl reload apache2
    echo "    Vhost enabled. Default site (000-default) was NOT touched."
else
    echo "==> Skipped vhost install (already present)."
fi

echo
echo "================================================================="
echo "  Setup complete — only $SITE_DIR was changed."
echo "  Other projects in /var/www/ were left untouched."
echo "================================================================="
echo
echo "Next steps:"
echo "  1) DNS — verify both records point at this server's Elastic IP:"
echo "       dig +short $DOMAIN"
echo "       dig +short www.$DOMAIN"
echo
echo "  2) HTTPS — issue the SSL cert (Let's Encrypt) :"
echo "       sudo certbot --apache -d $DOMAIN -d www.$DOMAIN \\"
echo "         --agree-tos -m info@$DOMAIN --redirect"
echo
echo "  3) Browse: https://$DOMAIN"
echo
echo "Optional — outbound mail from info@$DOMAIN:"
echo "  - Skipped automatic postfix install to avoid disturbing existing"
echo "    mail config on this server. If outbound mail isn't already"
echo "    working, install it manually:"
echo "       sudo apt install -y postfix mailutils   (choose 'Internet Site')"
echo "  - Or wire send_mail() in config/bootstrap.php to a real SMTP relay"
echo "    (Amazon SES, Mailgun, Postmark) by adding PHPMailer/Symfony Mailer."
