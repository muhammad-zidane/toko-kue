# Repository Guidelines

## Project Structure & Module Organization

Jagoan Kue is a Laravel 12 cake storefront requiring PHP 8.2+. Backend code lives in `app/`: HTTP controllers and middleware in `Http/`, Eloquent models in `Models/`, and checkout/cart logic in `Services/`. Keep business logic in services rather than expanding controllers.

Routes live in `routes/web.php` and `routes/auth.php`. Blade templates are in `resources/views/`; Vite sources are in `resources/css/` and `resources/js/`. Public assets live in `public/`, and uploaded images in `storage/app/public/`. Database migrations, factories, and seeders live in `database/`. Tests are under `tests/`; Docker configuration is in `compose.yaml` and `docker/`.

## Build, Test, and Development Commands

- `composer setup`: installs dependencies, creates `.env` if absent, generates the app key, migrates the database, and builds assets. Configure a local MySQL database first.
- `composer dev`: runs the Laravel server, queue listener, and Vite together.
- `npm run build`: builds production frontend assets.
- `php artisan storage:link`: exposes public uploads.
- `composer test`: clears cached configuration and runs PHP tests.
- `php artisan test --filter=CartTest`: runs a focused test selection.
- `npm test`: runs the Bun suite; requires Bun and an application at `http://localhost:8000`.
- `vendor/bin/pint --test`: checks PHP formatting; use `vendor/bin/pint` to format.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF, four-space indentation, and final newlines. YAML uses two spaces except `compose.yaml`, which uses four. Follow Laravel/Pint conventions, PascalCase PHP classes, camelCase methods, and descriptive migration names. Match neighboring Blade and JavaScript patterns; use existing layouts and partials for shared UI.

## Testing Guidelines

PHP tests use Pest/PHPUnit with `RefreshDatabase` enabled for both `Feature/` and `Unit/`. Name PHP tests `*Test.php` and Bun tests `*.test.ts`. Add regression coverage for changed behavior, including authorization and validation failures. No coverage threshold is configured.

PHP tests target the `testing` database. Bun helpers run `migrate:fresh --seed` against the configured database: use a disposable local database.

## Commit & Pull Request Guidelines

History generally uses `feat`, `fix`, `refactor`, `style`, and `chore`, with optional scopes, such as `fix(invoice): update store address`. Keep commits focused. PRs should describe behavior changes, link relevant issues, report validation, and include screenshots for UI changes. Call out migrations or configuration changes. Never commit `.env` or credentials.
