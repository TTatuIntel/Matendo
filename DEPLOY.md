# Deploying Matendo Health to AWS EC2 (Ubuntu) at `matendohealth.com`

Walk through this top-to-bottom on a fresh Ubuntu EC2 instance. Each step is
copy-paste ready. Lines starting with `$` run on your laptop; lines starting
with `ubuntu@ec2:~$` run on the server.

---

> **TL;DR** — once DNS is pointed at the EC2 IP and the repo is cloned to
> `/var/www/Matendo`, you can run the one-shot setup script:
>
> ```bash
> sudo bash /var/www/Matendo/deploy/setup.sh
> ```
>
> It installs Apache + PHP + MySQL + postfix, swaps in the production
> `.htaccess`, creates the database, generates `APP_KEY`/`APP_PEPPER`, writes
> `.env`, fixes permissions, and enables the vhost. Then run `certbot` (step 9
> below) for SSL. The numbered steps that follow are the manual equivalent if
> you want to do it by hand or audit each command.

---

## 0. Pre-flight checklist

- [ ] EC2 Ubuntu instance running (22.04 LTS or 24.04 LTS recommended).
- [ ] Security group allows **inbound TCP 22 (SSH), 80 (HTTP), 443 (HTTPS)**.
- [ ] Elastic IP attached to the instance (so it survives reboots).
- [ ] You can SSH in: `ssh -i your-key.pem ubuntu@<EC2-PUBLIC-IP>`.
- [ ] GitHub repo is public, OR you have a deploy key / PAT for cloning.

---

## 1. Point the domain at the EC2 instance

In your DNS provider (Route 53, Namecheap, Cloudflare, etc.) for
`matendohealth.com`, add **two A records**:

| Type | Name | Value | TTL |
|------|------|------|-----|
| A | `@` (or `matendohealth.com`) | `<EC2-Elastic-IP>` | 300 |
| A | `www` | `<EC2-Elastic-IP>` | 300 |

Wait 5–15 min, then verify from your laptop:

```bash
$ dig +short matendohealth.com
$ dig +short www.matendohealth.com
```

Both should return your EC2 IP. Don't continue until they do — Let's Encrypt
will fail otherwise.

---

## 2. Install the LAMP stack on the EC2 server

SSH in:

```bash
$ ssh -i your-key.pem ubuntu@<EC2-PUBLIC-IP>
```

Then on the server:

```bash
ubuntu@ec2:~$ sudo apt update && sudo apt upgrade -y

# Apache + PHP 8 + MySQL + the extensions this app uses
ubuntu@ec2:~$ sudo apt install -y \
    apache2 \
    mysql-server \
    php php-cli php-mysql php-mbstring php-xml php-curl php-fileinfo php-gd \
    libapache2-mod-php \
    git certbot python3-certbot-apache \
    unzip

# NOTE: postfix install is skipped here — install it manually only
# if outbound mail isn't already working on this server. Some shared
# servers already have a configured MTA, and re-installing postfix
# could change the system mail name and affect other apps.
#
# To install later:
#   sudo apt install -y postfix mailutils
# At the dialog: choose "Internet Site", system mail name "matendohealth.com".

# Enable Apache modules the .htaccess needs
ubuntu@ec2:~$ sudo a2enmod rewrite headers expires
ubuntu@ec2:~$ sudo systemctl restart apache2
```

Quick sanity check:

```bash
ubuntu@ec2:~$ php -v        # should show PHP 8.x
ubuntu@ec2:~$ apache2 -v    # Apache 2.4
ubuntu@ec2:~$ mysql --version
```

---

## 3. Clone the repo into `/var/www/Matendo`

```bash
ubuntu@ec2:~$ sudo mkdir -p /var/www
ubuntu@ec2:~$ cd /var/www
ubuntu@ec2:~$ sudo git clone https://github.com/TTatuIntel/Matendo.git
ubuntu@ec2:~$ sudo chown -R www-data:www-data /var/www/Matendo
```

If the repo is private, use SSH:

```bash
ubuntu@ec2:~$ sudo -u www-data ssh-keygen -t ed25519 -f /var/www/.ssh/id_ed25519 -N ""
ubuntu@ec2:~$ sudo cat /var/www/.ssh/id_ed25519.pub   # add to GitHub deploy keys
ubuntu@ec2:~$ sudo -u www-data git clone git@github.com:TTatuIntel/Matendo.git
```

---

## 4. Set up MySQL

```bash
ubuntu@ec2:~$ sudo mysql_secure_installation        # set root pw, remove test db, etc.

ubuntu@ec2:~$ sudo mysql
```

Inside the MySQL shell, create the database, the app user, and load the schema:

```sql
CREATE DATABASE matendo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Pick a strong password and keep it for the .env file in step 6.
CREATE USER 'matendo_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG';
GRANT SELECT, INSERT, UPDATE, DELETE ON matendo.* TO 'matendo_app'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Now load the schema:

```bash
ubuntu@ec2:~$ sudo mysql matendo < /var/www/Matendo/sql/schema.sql
ubuntu@ec2:~$ sudo mysql -e "USE matendo; SHOW TABLES;"   # verify 16 tables
```

---

## 5. Swap in the production `.htaccess`

The dev `.htaccess` is hardcoded for the XAMPP subpath. Replace it with the
production version that deploys at the domain root:

```bash
ubuntu@ec2:~$ cd /var/www/Matendo
ubuntu@ec2:/var/www/Matendo$ sudo mv .htaccess .htaccess.dev
ubuntu@ec2:/var/www/Matendo$ sudo cp deploy/.htaccess.production .htaccess
ubuntu@ec2:/var/www/Matendo$ sudo chown www-data:www-data .htaccess
```

---

## 6. Create the production `.env`

```bash
ubuntu@ec2:/var/www/Matendo$ sudo cp .env.production.example .env

# Generate APP_KEY (a 32-byte base64 string)
ubuntu@ec2:/var/www/Matendo$ php -r "echo base64_encode(random_bytes(32)).PHP_EOL;"

# Generate APP_PEPPER (64-hex-char string)
ubuntu@ec2:/var/www/Matendo$ php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"

# Edit the file: paste the generated values, set DB_PASS, MAIL_*, etc.
ubuntu@ec2:/var/www/Matendo$ sudo nano .env

# Lock it down — only Apache should read it.
ubuntu@ec2:/var/www/Matendo$ sudo chown www-data:www-data .env
ubuntu@ec2:/var/www/Matendo$ sudo chmod 640 .env
```

---

## 7. Make `storage/` writable

This is where logs and uploads land:

```bash
ubuntu@ec2:/var/www/Matendo$ sudo mkdir -p storage/logs storage/uploads
ubuntu@ec2:/var/www/Matendo$ sudo chown -R www-data:www-data storage
ubuntu@ec2:/var/www/Matendo$ sudo chmod -R 775 storage
```

---

## 8. Wire up the Apache vhost

Copy the vhost config that ships in `deploy/`:

```bash
ubuntu@ec2:~$ sudo cp /var/www/Matendo/deploy/matendohealth.conf \
                     /etc/apache2/sites-available/matendohealth.conf

# Disable the default placeholder site, enable ours.
ubuntu@ec2:~$ sudo a2dissite 000-default.conf
ubuntu@ec2:~$ sudo a2ensite matendohealth.conf

# Test the config and reload.
ubuntu@ec2:~$ sudo apache2ctl configtest         # should print "Syntax OK"
ubuntu@ec2:~$ sudo systemctl reload apache2
```

You should now be able to reach the site over **plain HTTP** (the HTTPS
redirect inside `.htaccess` won't fire yet because there's no HTTPS vhost):

```bash
$ curl -I http://matendohealth.com/         # expect 200 or a redirect
```

If `curl -I` returns 200, the site is live (insecurely). Move to step 9 to add
SSL.

---

## 9. Get a free SSL certificate via Let's Encrypt

```bash
ubuntu@ec2:~$ sudo certbot --apache \
    -d matendohealth.com -d www.matendohealth.com \
    --agree-tos -m info@matendohealth.com --redirect
```

`certbot` will:
1. Verify domain ownership over HTTP-01.
2. Issue a free cert (valid 90 days, auto-renewed).
3. Append a `<VirtualHost *:443>` block to `matendohealth.conf` with the cert paths.
4. Add a HTTP → HTTPS redirect.
5. Reload Apache.

Verify auto-renewal works:

```bash
ubuntu@ec2:~$ sudo certbot renew --dry-run
```

---

## 9b. (Recommended) Set up SPF + DKIM for the `info@matendohealth.com` mailbox

For mail from your server to actually land in inboxes (not spam), add these
DNS records at your domain provider:

| Type | Name | Value |
|------|------|-------|
| MX | `@` | `10 matendohealth.com.` (only if you also receive on this server) |
| TXT (SPF) | `@` | `v=spf1 a mx ip4:<EC2-Elastic-IP> ~all` |
| TXT (DMARC) | `_dmarc` | `v=DMARC1; p=none; rua=mailto:info@matendohealth.com` |

DKIM is more involved — for serious deliverability, use a relay like
**Amazon SES**, **Mailgun**, or **Postmark**, point `MAIL_HOST`/`MAIL_USER`/
`MAIL_PASS` in `.env` at the relay, and add the relay's DKIM records.

Quick sanity check from the server:

```bash
ubuntu@ec2:~$ echo "test from postfix" | mail -s "matendo test" your-personal@gmail.com
ubuntu@ec2:~$ tail -f /var/log/mail.log     # watch the delivery attempt
```

If postfix delivers but the message lands in spam, that's expected without
SPF/DKIM — fix the DNS records above.

---

## 10. Final smoke test

From your laptop:

```bash
$ curl -I https://matendohealth.com/             # expect HTTP/2 200
$ curl -I http://matendohealth.com/              # expect 301 to https
$ curl -I https://www.matendohealth.com/         # expect 301 to apex
$ curl -I https://matendohealth.com/about.php    # expect 301 to /about
$ curl -I https://matendohealth.com/about        # expect 200
$ curl -I https://matendohealth.com/api/login.php # expect 405 (POST-only) — proves api/ routing works
```

Open `https://matendohealth.com/` in a browser. You should see the home page
with the hero, navbar, and styled assets. Try:

- `/auth/register` → create a test account
- Click around all nav items
- Check the footer's social icons and legal links

---

## Updating the site later

When you push new commits to GitHub:

```bash
ubuntu@ec2:/var/www/Matendo$ sudo -u www-data git pull origin Matendo

# Cache-bust query strings in asset() update automatically (filemtime-based),
# but if you want to be explicit:
ubuntu@ec2:/var/www/Matendo$ sudo systemctl reload apache2
```

If you push schema changes, apply them manually:

```bash
ubuntu@ec2:/var/www/Matendo$ sudo mysql matendo < sql/migration_xyz.sql
```

---

## Troubleshooting

| Symptom | Likely cause / fix |
|---------|--------------------|
| `403 Forbidden` on every URL | `AllowOverride` not set to `All` in vhost, or `mod_rewrite` not enabled. `sudo a2enmod rewrite && sudo systemctl restart apache2`. |
| `500 Internal Server Error` | Check `/var/log/apache2/matendohealth-error.log` and `storage/logs/php-error.log`. |
| `Database connection failed` | `.env` `DB_*` values wrong, or MySQL not running: `sudo systemctl status mysql`. |
| Stylesheets/images 404 | Apache user can't read files: `sudo chown -R www-data:www-data /var/www/Matendo`. |
| `certbot` fails | DNS hasn't propagated yet (`dig +short matendohealth.com`), or port 80 is blocked at the security group. |
| Form submissions return 419 | CSRF mismatch — usually means the session cookie isn't sticking. Confirm `.env` has `APP_URL=https://matendohealth.com` (must match scheme). |
| `mod_php` directives ignored | If running PHP-FPM instead of mod_php, the `<IfModule mod_php.c>` block in `.htaccess` is silently skipped — you'll set those values in `/etc/php/8.x/fpm/php.ini` instead. |

---

## Security follow-ups (do these within the first week)

1. **Disable password SSH** — use only the `.pem` key.
   `sudo nano /etc/ssh/sshd_config` → `PasswordAuthentication no` → `sudo systemctl restart ssh`.
2. **Enable UFW**:
   `sudo ufw allow OpenSSH && sudo ufw allow "Apache Full" && sudo ufw enable`.
3. **Set up automatic security updates**: `sudo apt install unattended-upgrades`.
4. **Snapshot the EBS volume** so you can roll back if a deploy goes wrong.
5. **Rotate `APP_KEY` / `APP_PEPPER`** away from the dev values committed in the repo's `.env`. Old user passwords still work because the pepper is mixed into the bcrypt hash, but any new accounts will use the new pepper. If you want, force a password reset for everyone.

---

That's it. Site should be live at `https://matendohealth.com/` with HTTPS, clean URLs, and all the features (auth, forms, dashboard) working.
