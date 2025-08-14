# Deployment Status Update (Rewritten)

## Executive Summary
- I migrated the project into a full Laravel app (`matendo_health/`), moved HTML pages and endpoints into Laravel controllers, and deployed to Vercel.
- Frontend assets are built via Vite, and the app currently uses SQLite locally and PlanetScale (MySQL) remotely.
- The `join` endpoint has been fully ported; remaining flows (e.g., facility registration) still need routing/controller updates.
- Work took two weekends due to incremental discovery and refactoring required by the migration.

---

## Current State

- __Codebase__
  - Laravel app lives in `matendo_health/`.
  - Vite is used for assets; manifest at `public/build/manifest.json`.
  - Controllers created for previously static/endpoint functionality (e.g., `JoinController`).
  - Blade templates replace raw HTML files where applicable.

- __Deployment__
  - Deployed to Vercel using `vercel.json` for routing and PHP runtime config.
  - Using Vite build outputs on deploy.

- __Database__
  - Local dev: SQLite at `database/database.sqlite` (as per memory).
  - Production: PlanetScale (MySQL). Env configured via `.env` for `DB_CONNECTION=mysql`.
  - Note: Statement “possibly change this to sql” likely meant “standardize on MySQL” or “fallback to SQLite for local.” Currently we use:
    - SQLite for local development.
    - PlanetScale MySQL for production.

---

## Architecture (High-Level)

```
+----------------------+
|     Client (Web)     |
|  - Blade + Vite      |
|  - /login, /join,    |
|    facility pages    |
+----------+-----------+
           |
           v
+----------------------+
|   Laravel (Vercel)   |
|  - Routes/web.php    |
|  - Controllers       |
|  - Middleware/Auth   |
+----------+-----------+
           |
           v
+----------------------+
|  Database Layer      |
|  - Local: SQLite     |
|    database.sqlite   |
|  - Prod: PlanetScale |
|    (MySQL)           |
+----------------------+
```

---

## Deployment Workflow

```
Local Dev
  |
  | vite dev / npm run dev
  v
Git Push (main)
  |
  v
Vercel Build
  - npm ci && npm run build
  - PHP runtime boot
  - publish .output/public or public/build assets
  |
  v
Vercel Deploy (Prod/Preview)
```

---

## Changes Completed

- __Framework Migration__
  - Converted static pages and ad hoc endpoints into Laravel MVC (`routes/web.php`, `app/Http/Controllers/*`, `resources/views/*`).
  - Asset pipeline refactored to Vite.

- __Endpoints__
  - `join` endpoint fully migrated and tested on Vercel.
  - Other flows identified but pending migration (see Next Steps).

- __Environment/Config__
  - `.env` configured for:
    - Local: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`.
    - Production: `DB_CONNECTION=mysql` with PlanetScale credentials.
  - `config/database.php` supports both SQLite and MySQL.

- __Deployment__
  - Vercel configuration added via `vercel.json`.
  - Initial manual deploy performed via Vercel CLI.

---

## Outstanding Issues

- __Routing parity__
  - Facility pages/flows need routes and controllers aligned with Laravel.
  - Some legacy links still point to old paths.

- __Styling__
  - `/login` and some pages have CSS issues (likely due to asset path changes post-Vite/Laravel).

- __Automation__
  - CI/CD not yet configured; deploys rely on CLI/manual triggers.

- __Domain__
  - App not yet bound to production domain.

---

## Risks/Constraints

- __Schema drift__
  - SQLite (dev) vs PlanetScale (prod) can drift if migrations are not consistently applied.
- __Migrations__
  - Prior duplicate column issue on `tasks.medical_conditions` requires care when running in prod.
- __Vite assets__
  - If `npm run build` is skipped/misconfigured, asset 404s or missing styles/scripts can occur.

---

## Next Steps (Detailed)

1. __Refactor folder structure__
   - Goal: Flatten unnecessary nesting and standardize Laravel conventions.
   - Actions:
     - Move legacy assets/templates into `resources/views/` and `resources/js|css/`.
     - Remove orphaned static files in `public/` that duplicate Vite outputs.
     - Ensure consistent route names and URL generation via `route()`.

2. __Map domain to Vercel__
   - Point `matendohealth.*` (exact domain you own) to the Vercel project.
   - Configure HTTPS and environment variables on Vercel.

3. __Fix facility pages__
   - Create controllers (e.g., `FacilityController`) and routes for:
     - `GET /facility/register` (form)
     - `POST /facility/register` (submission)
     - `GET /facility/{id}` (detail)
   - Update Blade templates to use `{{ route('...') }}` helpers.

4. __Fix /login and CSS__
   - Confirm `@vite(['resources/css/app.css','resources/js/app.js'])` in `resources/views/layouts/app.blade.php`.
   - Check Vite build on Vercel and the published manifest at `public/build/manifest.json`.
   - Normalize auth routes (Laravel Breeze/Fortify) if applicable.

5. __Set up automatic deployments__
   - Connect repo to Vercel for zero-CLI deploys on push.
   - Add environment variables for PlanetScale in Vercel dashboard.
   - Optional: Add GitHub Actions for `php artisan test`, `npm ci && npm run build`, and lint checks.

6. __Database consistency__
   - Ensure all migrations run in PlanetScale (prod) and locally.
   - Add guards for existing columns (to avoid the previous duplicate column migration issue).
   - Decide on definitive prod DB: keep PlanetScale (recommended) and reserve SQLite for local only.

---

## Helpful References in Repo

- __Laravel app root__: `matendo_health/`
- __Env files__: `.env`, `.env.example`
- __DB config__: `config/database.php`
- __Vercel config__: `vercel.json`
- __Vite manifest__: `public/build/manifest.json`
- __Local SQLite__: `database/database.sqlite`

---

## Timeline Note
This effort spanned two weekends due to staged migration (peeling the onion): moving static assets to Blade, rebuilding routes/controllers, configuring Vite in Laravel, and aligning deployment/runtime on Vercel.

---

## Optional: Example .env (Prod on PlanetScale)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=<planetscale-host>
DB_PORT=3306
DB_DATABASE=<db-name>
DB_USERNAME=<username>
DB_PASSWORD=<password>

VITE_PUSHER_APP_KEY=
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=
```

---

# Summary of Task Status
- Rewrote your update into a structured, detailed, and accurate status with diagrams.
- Clarified current setup (Laravel + Vite, Vercel, SQLite local, PlanetScale prod).
- Listed concrete next steps to reach stable production with proper routing, CSS, domain, and CI/CD.
