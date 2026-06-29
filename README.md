# Manajemen Gudang Cloud

Warehouse management system built with Laravel 13, deployed on Google Cloud Run.

## Stack

| Layer         | Technology                                      |
| ------------- | ----------------------------------------------- |
| Framework     | Laravel 13 / PHP 8.3+                           |
| Frontend      | Vite 8 / Tailwind CSS 4                         |
| Database      | SQLite (default), MySQL (production)            |
| Auth          | Firebase Authentication                         |
| Storage       | Google Cloud Storage (via Spatie GCS driver)    |
| Secrets       | Google Secret Manager                           |
| CI/CD         | Google Cloud Build (`cloudbuild.yaml`)          |
| Testing       | PHPUnit 12 (in-memory SQLite)                   |
| Linting       | Laravel Pint (Laravel preset)                   |
| Deployment    | Docker (FrankenPHP + Octane) → Google Cloud Run |

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 24+ & npm
- Docker (for production build / Cloud Run deployment)

## Quick Start

```bash
composer setup
```

Runs everything: installs PHP + JS deps, generates `.env` + app key, runs migrations, builds frontend assets.

## Commands

### Development

```bash
# Full dev stack (artisan serve + queue worker + log tail + vite HMR)
composer dev

# Dev stack without log tail
composer dev-np

# Frontend only (Vite dev server)
npm run dev
```

### Testing

```bash
# Run all tests (clears config cache first)
composer test

# Run a single test
php artisan test --filter=TestName
```

Tests use **in-memory SQLite** — no external database needed.

### Code Formatting

```bash
# Format with Laravel Pint
vendor/bin/pint

# Check without fixing
vendor/bin/pint --test
```

### Frontend Build

```bash
# Production build
npm run build
```

## Production Deployment (Google Cloud Run)

Multi-stage Docker build. Stage 1 compiles frontend assets (Vite). Stage 2 packs FrankenPHP + Octane worker mode. Secrets are never baked into the image.

### Local Development (Docker Compose)

```bash
# Build and run full stack (app + MySQL)
docker-compose up --build

# Stop
docker-compose down
```

`docker-compose.yml` uses explicit environment variables — no blanket `.env` import.

### Production Build

```bash
# Build the image (requires .env.production)
cp .env.production.example .env.production
# Fill in Firebase config values, then:
docker build -t manajemen-gudang .
```

### Deploy via Cloud Build

Push to `main` branch. `cloudbuild.yaml` pipeline:
1. Fetches Firebase client config from Secret Manager → writes `.env.production`
2. Docker build (no `--build-arg` leakage)
3. Push to Artifact Registry
4. Deploy to Cloud Run with runtime secrets mounted via `--set-secrets`

### Environment Variables

**Local dev:** `.env` file at project root (gitignored, dockerignored).

**Production (Cloud Run):**

| Category | Variable | Source |
|---|---|---|
| **Non-sensitive** | `APP_ENV`, `APP_DEBUG`, `LOG_CHANNEL` | Cloud Run env vars |
| **Secrets** | `APP_KEY`, `DB_PASSWORD`, `DB_HOST`, GCS creds, mail creds | Secret Manager → `--set-secrets` |
| **Frontend (public)** | `VITE_FIREBASE_*` | Secret Manager → `.env.production` file (build-time only, `rm`'d after Vite compiles) |

Firebase `VITE_FIREBASE_*` values are **project identifiers**, not secrets. Firebase docs state they're safe to include in client code. Authorization is handled by Firebase Security Rules + App Check.

### GCS Storage Disk

Pre-configured `gcs` disk in `config/filesystems.php`. Uses Uniform Bucket-Level Access. Credentials pulled from GCP IAM (Secret Manager) — no JSON key files.

```php
Storage::disk('gcs')->put('file.txt', $contents);
```

### Pre-Deployment Checklist

1. [ ] Set up GCP project with billing enabled
2. [ ] Enable APIs: Cloud Run, Cloud Build, Secret Manager, Artifact Registry, Cloud SQL, Cloud Storage
3. [ ] Create Artifact Registry repository
4. [ ] Create Secret Manager secrets for all runtime vars (`app-key`, `db-password`, `db-host`, `gcs-project-id`, etc.)
5. [ ] Create a composite secret `firebase-client-config` with all `VITE_FIREBASE_*` values as an `.env.production`-formatted payload
6. [ ] Set up Cloud SQL MySQL instance (or Compute Engine MySQL)
7. [ ] Configure `cloudbuild.yaml` substitutions for your region, memory, CPU
8. [ ] Connect Cloud Build to your GitHub repo
9. [ ] Create Cloud Build trigger on `main` branch push

## Project Structure

```
app/
  Http/Controllers/   — controllers
  Models/             — Eloquent models
config/
  filesystems.php     — local, public, s3, gcs disks
database/
  migrations/         — database migrations
resources/
  views/              — Blade templates
  css/app.css         — Tailwind entry
  js/app.js           — JS entry
routes/
  web.php             — web routes
  api.php             — API routes
tests/
  Feature/            — feature tests
  Unit/               — unit tests
Dockerfile            — multi-stage production build
.dockerignore         — Docker build context exclusions
.env.production.example — template for build-time frontend config
cloudbuild.yaml       — CI/CD pipeline (Cloud Build)
docker-compose.yml    — local dev stack (app + MySQL)
docker/
  entrypoint.sh       — boot script (config:cache, migrate, start)
```

## License

MIT
