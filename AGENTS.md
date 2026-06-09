# AGENTS.md — Manajemen Gudang Cloud

## Stack

- **Laravel 13** / PHP 8.3+ / Vite 8 / Tailwind CSS 4
- SQLite default (`.env.example`), in-memory SQLite for tests
- PHPUnit 12, Laravel Pint (no custom config — uses Laravel preset)
- Deploy: Azure Web Apps via GitHub Actions (`main` branch)
- `.npmrc` sets `ignore-scripts=true` — `npm install` skips lifecycle scripts

## Commands

| Task | Command |
|------|---------|
| First-time setup | `composer setup` (installs deps, generates .env + app key, runs migrations, builds assets) |
| Full dev stack | `composer dev` (artisan serve + queue:listen + pail + vite, concurrently) |
| Run tests | `composer test` (clears config, then `php artisan test`) |
| Single test | `php artisan test --filter=TestName` |
| Format code | `vendor/bin/pint` |
| Frontend build | `npm run build` |
| Frontend dev | `npm run dev` |

## Conventions

- **Model attributes**: Use PHP 8 attributes for `$fillable` and `$hidden` — `#[Fillable([...])]`, `#[Hidden([...])]`. This is Laravel 13 style, not the old `$fillable` property.
- **4-space indent**, UTF-8, LF line endings, final newline (`.editorconfig`).
- Tests run against **in-memory SQLite** (`phpunit.xml`) — no external DB needed.

## Structure

- `app/Http/Controllers/` — controllers
- `app/Models/` — Eloquent models (currently only `User.php`)
- `routes/web.php` — web routes (no `api.php` yet)
- `database/migrations/` — migrations
- `resources/views/` — Blade templates
- `resources/css/app.css` — entry CSS (Tailwind)
- `resources/js/app.js` — entry JS
