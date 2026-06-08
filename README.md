# LaraAction API Boilerplate

An enterprise-ready, AI-driven Laravel API boilerplate designed for scalability and maintainability. This starter kit strictly adheres to the **Action Pattern**, ensuring thin controllers and isolated business logic.

Built with modern PHP standards and ready to be extended by AI assistants using the provided `AGENTS.md` guidelines.

## 🚀 Key Architectural Principles

* **Thin Controllers & Action Pattern:** Controllers are solely responsible for HTTP routing. All business logic is strictly encapsulated within `app/Actions/`.
* **Strict Type-Hinting:** Built for modern PHP (`declare(strict_types=1);` enforced across the codebase).
* **Isolated Validation:** Zero validation logic in controllers or actions. Strictly utilizing Laravel `FormRequest`.
* **Standardized API Responses:** A unified JSON response structure for both successful operations and error handling.
* **Stateless Authentication:** Powered by `Laravel Sanctum` for secure API token management.
* **Robust RBAC:** Integrated with `spatie/laravel-permission` for granular Role-Based Access Control.

## 📦 Out-of-the-Box Features

* **API Versioning:** Pre-configured `v1` routing (`/api/v1/*`).
* **Core Endpoints:** Ready-to-use routes for `health`, `auth`, `users`, `roles`, and `permissions`.
* **Pre-seeded Roles:** Comes with `SuperAdmin`, `Manager`, and `User` roles.
* **Granular Permissions:** Pre-defined permissions for basic CRUD operations and role/permission assignments.
* **Dev-Ready Admin Account:** Automatically provisions a `SuperAdmin` account in `local` and `testing` environments for immediate access.

## 🤖 AI-Agent Integration (`AGENTS.md`)

This boilerplate includes an `AGENTS.md` file at the root directory. It contains strict system prompts and trigger commands designed for AI coding assistants (like Cursor, GitHub Copilot Workspace, or ChatGPT). By using these guidelines, the AI will automatically generate new modules, write tests, and update documentation while strictly maintaining the project's architectural standards.

## 🛠️ Local Setup & Installation

1.  Clone the repository and install dependencies:
    ```bash
    composer install
    ```
2.  Configure your environment:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3.  Set up your `MySQL/MariaDB` credentials in the `.env` file.
4.  Run migrations and seed the database:
    ```bash
    php artisan migrate --seed
    ```
5.  Start the development server:
    ```bash
    php artisan serve
    ```

**Default Development Account:**
* **Email:** `superadmin@example.com`
* **Password:** `password`

## 🛡️ Quality Control & Testing

Maintain code quality and ensure everything runs smoothly before deploying.

* **Code Formatting:** Ensure PSR-12 compliance and clean code style.
    ```bash
    php artisan pint
    ```
* **Run Test Suite:**
    ```bash
    php artisan test
    ```
    *Note: The automated test suite utilizes `SQLite in-memory` configured via `phpunit.xml` for fast execution.*

---
**Testing Note (Pest vs PHPUnit):**
The initial target for this project was the Pest testing framework. However, due to current compatibility issues between Laravel 13 + PHP 8.3 and the available stable releases of Pest, the test suite currently relies on native `PHPUnit`. This will be migrated to Pest once the upstream dependencies are fully aligned.