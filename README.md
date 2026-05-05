# Matendo Medics

Healthcare staffing and personal-care marketplace. Vanilla PHP 8.2+ with a Toptal-style two-sided model: facilities and patients on one side, vetted medical professionals on the other.

## Architecture

```
.
├── config/          Bootstrap, .env loader, PDO singleton, CSRF + crypto helpers
├── includes/        Shared header, footer, flash messages
├── api/             JSON endpoints (CSRF + rate-limited): facility/care/join/contact/newsletter
├── auth/            Sign in / register / reset / dashboard / logout
├── legal/           Privacy, terms, security pages
├── assets/css       base.css · components.css · pages.css
├── assets/js        main.js (site-wide UI) · forms.js (modals + secure submit)
├── sql/schema.sql   Unified MySQL schema (replaces the two divergent legacy DBs)
├── storage/         Private file uploads + log files (gitignored)
├── images/          Static images (existing)
└── *.php            Page entry points: index, hire, join, talent, professional,
                     about, contact, careers
```

Every page boots from `config/bootstrap.php`, which loads `.env`, hardens the
session, sets security headers, and exposes helpers (`csrf()`, `csrf_field()`,
`e()`, `redirect()`, `json_response()`, `current_user()`, `require_auth()`).

## Setup

1. **Install requirements**
   - PHP 8.2+ (bundled with XAMPP)
   - MySQL 8.0+ / MariaDB 10.6+
   - Apache with `mod_rewrite`, `mod_headers`, `mod_expires` enabled

2. **Create the database**
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
   Then create a least-privileged DB user:
   ```sql
   CREATE USER 'matendo_app'@'localhost' IDENTIFIED BY '<strong_password>';
   GRANT SELECT, INSERT, UPDATE, DELETE ON matendo.* TO 'matendo_app'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Configure `.env`**
   ```bash
   cp .env.example .env
   php -r "echo 'APP_KEY=' . base64_encode(random_bytes(32)) . PHP_EOL;"
   php -r "echo 'APP_PEPPER=' . bin2hex(random_bytes(32)) . PHP_EOL;"
   ```
   Paste the printed values into `.env` and fill in DB + SMTP credentials.

4. **Open the site**
   <http://localhost/tattuintel/Matendo/>

## Security model

| Concern               | Implementation |
|-----------------------|----------------|
| SQL injection         | PDO prepared statements only; emulated prepares disabled |
| CSRF                  | Per-session token, validated on every POST and JSON endpoint |
| File upload abuse     | Extension + MIME whitelist + `finfo` magic-byte check + size cap; files moved to private storage with random names |
| PHI at rest           | `medical_conditions`, `medications`, `allergies` encrypted with AES-256-GCM via `Security::encrypt()` |
| Password storage      | Argon2id + server-side pepper |
| Brute force           | DB-backed rate limiting per IP/route; account lockout after 5 failed logins |
| Session hijacking     | HttpOnly + SameSite=Lax cookies, `session_regenerate_id()` on auth state change |
| Headers               | CSP, X-Content-Type-Options, X-Frame-Options=DENY, Referrer-Policy, Permissions-Policy (see `.htaccess`) |
| Information leakage   | `display_errors` off; errors logged to `storage/logs/php-error.log` |
| Direct file access    | `Require all denied` in `config/`, `includes/`, `sql/`, `storage/` |

## What changed from the legacy site

| Legacy                                             | Replacement                                  |
|----------------------------------------------------|----------------------------------------------|
| 6 static `.html` pages with inline CSS+JS          | 8 PHP pages using shared header/footer       |
| Two divergent databases (`matendo`, `matendo_medics`) | Single `matendo` schema in `sql/schema.sql` |
| `save_facility_request.php` (root creds, escaping AND prepared, BLOB uploads) | `api/facility-request.php` (env creds, CSRF, MIME-checked uploads to private storage) |
| `save_personal_care_request.php` (PHI in plaintext) | `api/care-request.php` (PHI encrypted at rest) |
| `join.php` legacy (htmlspecialchars-on-store, BLOBs) | `api/join-application.php` + new `join.php` page |
| `localhost:8000/login` placeholder                 | Real `auth/login.php` + `register.php` + `dashboard.php` + `forgot.php` |
| No talent directory                                 | `talent.php` search + filters; `professional.php` profiles |

Old HTML pages are preserved in git history (commit `d644cad6`); restore with `git show d644cad6:index.html`.

## Roadmap (Toptal-style)

- **Phase 1 — done:** secure foundation, accounts, dashboards, talent directory.
- **Phase 2:** matching workflow (admin shortlists three pros per request), in-app messaging, two-way reviews, document download with audit trail.
- **Phase 3:** Stripe Connect + M-Pesa Daraja for escrow and payouts; timesheets and auto-invoicing; e-signed contracts.
- **Phase 4:** SSO (Google, LinkedIn) + MFA, public REST API, blog/CMS, EN/SW localisation, mobile app.

## Development tips

```bash
php -l <file>             # syntax check a single file
php -S localhost:8000     # quick dev server (use XAMPP for .htaccess)
mysql -u matendo_app -p matendo
```

Logs: `storage/logs/php-error.log`.
