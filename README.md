# Hamro English Boarding School Platform

Laravel 11 backend and React + TypeScript frontend for the school website and CMS.
The frontend and backend are deliberately separated: Laravel exposes JSON APIs and
React owns the browser UI, navigation, and client state.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
```

In a second terminal, run `php artisan serve` and open `http://127.0.0.1:8000`.

The public website is available at `/`. The administration overview is available at `/admin`.

## Architecture

```text
Browser
  React SPA (resources/js)
        |
        | JSON over /api/v1
        v
  Laravel API (routes/api.php, app/Http/Controllers)
        |
        v
  MySQL (Eloquent models and migrations)
```

- `routes/api.php` is the only application data boundary. It is mounted at `/api/v1`.
- `routes/web.php` serves the small Blade shell in `resources/views/app.blade.php`.
- `resources/js/app.tsx` maps browser paths to React pages and loads API data.
- `resources/js/runtime.tsx` contains the small React navigation, page context, and JSON form client.
- `app/Http/Controllers/Public` contains public read/write API controllers; `Admin` contains authenticated CMS APIs.
- Authentication uses Laravel's session guard and CSRF protection; the React client sends `Accept: application/json`.

For a new feature, add a Laravel API route/controller and JSON response first, then add the matching
React route/page. Do not add server-rendered Inertia pages or Blade feature views.

## Project structure

```text
app/                  Laravel controllers, models, and providers
bootstrap/             Laravel application bootstrap
config/                Laravel configuration
database/              Migrations, factories, and seeders
resources/js/          React pages, layouts, components, and TypeScript types
resources/css/         Public and admin styles
resources/views/       React application shell
routes/                API and frontend shell routes
tests/                 PHPUnit feature and unit tests
```

## Verification

```bash
composer test
npm run build
php artisan migrate:fresh --seed
```

The seeded foundation includes school settings, the Academic Navy palette, and the initial academic programs.

## CI/CD and AlmaLinux deployment

CI builds and tests the application entirely in Docker. Successful `main` builds are published to GHCR,
then the self-hosted AlmaLinux runner pulls and deploys the exact image digest with Docker Compose. The
SQLite database and uploads remain in a persistent host directory, and a failed health check triggers an
automatic application-image rollback.

Follow the complete [AlmaLinux production deployment guide](docs/deployment.md). The checked-in
[production environment template](.env.production.example) lists the required Laravel runtime settings.
