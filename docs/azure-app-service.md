# Azure App Service Deployment

This app is a Laravel app deployed to Azure App Service Linux with PHP 8.4.

## Required App Service Configuration

Set **Configuration > General settings > Startup Command** to:

```bash
bash /home/site/wwwroot/startup.sh
```

Laravel serves requests from `public/`, but the Azure PHP Linux image starts NGINX at `/home/site/wwwroot` by default. `startup.sh` patches the container NGINX config on every restart so the document root becomes `/home/site/wwwroot/public` and Laravel routes are sent to `index.php`.

## Required App Settings

Set these in **Configuration > Environment variables**:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://manajemengudang-ecfabth4b8bdf3eh.southeastasia-01.azurewebsites.net
APP_KEY=<generated Laravel key>
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

In Azure Portal, set `APP_KEY` as:

```text
Name: APP_KEY
Value: base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
```

If Azure logs still show `Encrypter->__construct('APP_KEY=base64:...', ...)` after fixing the setting, restart the App Service. `startup.sh` removes cached Laravel config from `bootstrap/cache/config.php` so stale settings are not reused.

Keep `APP_URL` on `https://`. Azure terminates TLS before PHP, and `bootstrap/app.php` trusts Azure's `X-Forwarded-Proto` headers so Laravel generates HTTPS asset URLs instead of blocked mixed-content HTTP URLs.

Generate `APP_KEY` once with:

```bash
php artisan key:generate --show
```

Do not commit `.env`. Azure App Settings replace `.env` in production.

## Deployment Source

Use one deployment source only. Prefer GitHub Actions with `.github/workflows/azure-webapps-php.yml`. Disable Azure Deployment Center External Git to avoid older deployments overwriting the GitHub Actions artifact.
