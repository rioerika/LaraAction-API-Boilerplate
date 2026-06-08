# LaraAction API Boilerplate

LaraAction is a production-minded Laravel API boilerplate built to prevent bloated controllers in growing applications. It enforces the Action Pattern, isolated validation, strict typing, and consistent JSON contracts so the codebase stays predictable, testable, and easy to extend.

## Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP 8.3+
- **Authentication:** Laravel Sanctum
- **Access Control:** Spatie Laravel Permission
- **Testing:** PHPUnit, with Pest migration planned when upstream compatibility is ready
- **Code Style:** Laravel Pint

## Architecture

This boilerplate follows a strict API-first flow:

`Route -> Thin Controller -> FormRequest -> Action -> Standardized JSON Response`

- **Routes** map HTTP requests to versioned API endpoints under `/api/v1`.
- **Controllers** stay thin and only orchestrate requests, actions, and responses.
- **FormRequests** own validation and request authorization.
- **Actions** hold business logic under `app/Actions`.
- **Responses** always follow the same JSON envelope.

### Response Contract

Successful responses:

```json
{
  "success": true,
  "message": "Descriptive message",
  "data": {}
}
```

Failed responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {}
}
```

## Included Features

- Versioned API routing under `/api/v1`
- Health endpoint for application liveness checks
- Readiness endpoint for database and cache dependency checks
- Sanctum-based login, logout, and authenticated profile endpoints
- RBAC foundation with roles, permissions, and protected resource routes
- Internal audit trail for RBAC mutations, including actor, subject, and before/after snapshots
- Swagger / OpenAPI documentation with interactive UI and generated JSON spec
- Reference modules for `users`, `roles`, and `permissions`
- Default seeded roles: `SuperAdmin`, `Manager`, and `User`
- Development bootstrap account for `local` and `testing` environments
- Internal docs in `docs/` for PRD, FSD, ERD, and module workflow

## AI-Assisted Development

This repository is structured to work well with AI coding assistants. The root-level `AGENTS.md` file defines architectural rules, naming conventions, and trigger-based workflows for repetitive tasks such as:

- creating a new module
- writing tests
- synchronizing the ERD documentation

The current repository is **AI-guided**, not AI-self-generating. The conventions are explicit enough for agents to follow consistently, while tests and quality gates provide verification for generated changes.

## Local Setup

1. Install dependencies:

   ```bash
   composer install
   ```

2. Prepare the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure `MySQL/MariaDB` credentials in `.env`.
4. Run migrations and seed the database:

   ```bash
   php artisan migrate --seed
   ```

5. Start the development server:

   ```bash
   php artisan serve
   ```

Default development account:

- **Email:** `superadmin@example.com`
- **Password:** `password`

## Quality Gates

Format code:

```bash
composer format
```

Run formatting checks only:

```bash
composer lint
```

Run the minimum verification gate:

```bash
composer quality
```

Run static analysis directly:

```bash
composer analyse
```

Generate Swagger / OpenAPI documentation:

```bash
composer docs:generate
```

Run tests directly:

```bash
php artisan test
```

The automated test suite uses `SQLite in-memory` via `phpunit.xml` for fast execution.

## CI / CD

GitHub Actions is disabled by default in this boilerplate.

If you want to enable it later, use the provided template at `docs/templates/ci.github-actions.yml.example` and follow the activation notes in `docs/CI_SETUP.md`.

For deployment, use the separate template at `docs/templates/cd.github-actions.yml.example` and the activation guide in `docs/CD_SETUP.md`.

The intended minimum CI quality gate remains:

- `composer lint`
- `composer analyse`
- `composer test`

## API Documentation

- Swagger UI: `/api/docs`
- OpenAPI JSON: `/api/docs.json`
- Generator command: `composer docs:generate`

The Swagger UI is intended for development and internal integration use. Authenticated endpoints use the Sanctum bearer token scheme in the `Authorization` header.

## Notes

- The health endpoint is currently a liveness check, not a full readiness check.
- The readiness endpoint checks database connectivity and cache round-trip availability.
- Pest is not enabled yet because the stable ecosystem support for the current Laravel 13 + PHP 8.3 combination is not fully aligned in this environment.
