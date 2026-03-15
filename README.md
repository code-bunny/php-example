# php-app

A PHP web application with a JSON:API, an admin panel, and a public site. Built to demonstrate how a well-structured PHP app works without a framework.

## Prerequisites

Skip any steps you have already completed.

**1. Install Docker Desktop**

Download and install from https://www.docker.com/products/docker-desktop

**2. Install Homebrew** (macOS)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**3. Install PHP**

```bash
brew install php
```

**4. Install Xdebug** (required for coverage reports)

```bash
pecl install xdebug
```

## Setup

```bash
cp .env.example .env
```

The defaults work with the Docker setup below. Edit `.env` before deploying.

Start the database (MySQL) and phpMyAdmin:

```bash
docker compose up -d
```

Install PHP dependencies:

```bash
composer install
```

Run migrations:

```bash
php migrate.php
```

Seed the database with sample data and the first admin user:

```bash
php seeds.php
```

Start the dev server:

```bash
bin/serve
```

| URL | Description |
|-----|-------------|
| http://localhost:8000 | Public site |
| http://localhost:8000/admin | Admin panel |
| http://localhost:8080 | phpMyAdmin |
| http://localhost:8081 | Swagger UI |

## Admin panel

Sign in at http://localhost:8000/admin/login with the credentials created by `seeds.php` (default: `admin@example.com` / `password`). Change the password after first sign in.

The admin panel lets you manage posts, contacts, subscribers, API keys, and users.

## Console

Open an interactive shell with all models pre-loaded (like `rails console`):

```bash
bin/console
```

Use `APP_ENV=test bin/console` to open against the test database.

```
> User::all()
> Post::count()
> $u = new User(['email' => 'someone@example.com', 'role' => 'admin'])
> $u->setPassword('secret123')
> $u->save()
```

## API keys

All API endpoints require a Bearer token. Create one in the admin panel at `/admin/api_keys`, then pass it with every request:

```
Authorization: Bearer <your-api-key>
```

In Swagger UI, click **Authorize 🔒**, paste the token, and click **Authorize**.

## Testing

Run the full test suite:

```bash
bin/test
```

The test server starts automatically on port 8001 — the dev server on 8000 keeps running.

Run a specific suite, file, or test:

```bash
bin/test --testsuite API
bin/test tests/api/v1/PostsTest.php
bin/test --filter test_create_post
```

Generate an HTML coverage report (opens in the browser):

```bash
bin/coverage
```

Tests use a separate `mydb_test` database and clean up after themselves.

## Databases

The app follows the Rails convention — `DB_NAME` in `.env` is the base name and the environment is appended automatically:

| Environment | Database |
|-------------|----------|
| development | mydb_development |
| test | mydb_test |

Run migrations against both:

```bash
php migrate.php
APP_ENV=test php migrate.php
```

## Project structure

```
├── index.php          # Front controller — all requests go through here
├── router.php         # PHP built-in server router
├── migrate.php        # Runs pending migrations
├── seeds.php          # Seeds sample data and the first admin user
├── models/            # Model classes (Post, Contact, Subscriber, ApiKey, User)
├── pages/             # Page templates
│   ├── admin/         # Admin panel pages
│   └── api/           # JSON:API resource handlers
├── helpers/           # csrf, flash, rate limiting, auth, env, logging
├── migrations/        # Versioned schema migrations
├── tests/             # PHPUnit test suite
│   ├── unit/          # In-process unit tests
│   ├── api/v1/        # Request tests for the JSON:API
│   └── pages/         # Request tests for HTML pages (including admin)
├── bin/
│   ├── serve          # Start the dev server (localhost:8000)
│   ├── console        # Interactive shell (PsySH)
│   ├── test           # Run the test suite
│   └── coverage       # Run tests and generate an HTML coverage report
└── docker/
    └── init.sql       # Creates mydb_development and mydb_test databases
```
