# php-example

## Prerequisites

Skip any steps you have already completed: You may already have Homebrew, PHP, and Docker installed.

**1. Install Docker Desktop**

Download and install from https://www.docker.com/products/docker-desktop

**2. Install Homebrew**

Homebrew is a package manager for macOS. Open Terminal and run:

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**3. Install PHP**

```bash
brew install php
```

Verify it worked:

```bash
php -v
```

## Setup

Copy the environment file:

```bash
cp .env.example .env
```

The defaults work with the Docker setup below. Edit `.env` to change credentials before deploying.

Start the database (MySQL) and phpMyAdmin:

```bash
docker compose up -d
```

Run migrations:

```bash
php migrate.php
```

Seed the database with sample posts:

```bash
php seeds.php
```

Start the dev server:

```bash
php -S localhost:8000 router.php
```

The app is at http://localhost:8000. phpMyAdmin is at http://localhost:8080. Swagger UI is at http://localhost:8081.

The admin panel is at http://localhost:8000/admin — username: `admin`, password: `secret`.

## Testing

Install dependencies (first time only):

```bash
composer install
```

The tests make real HTTP requests to the running app, so **start the dev server first**:

```bash
php -S localhost:8000 router.php
```

Then, in a second terminal tab, run the full test suite:

```bash
bin/test
```

Run just one suite:

```bash
bin/test --testsuite API
bin/test --testsuite Pages
```

Run a single test file:

```bash
bin/test tests/api/v1/PostsTest.php
```

Run a single test by name:

```bash
bin/test --filter test_create_post
```

Tests clean up after themselves — any records created during a test are deleted when it finishes.

## API Keys

All API endpoints require a Bearer token. To generate one:

1. Go to http://localhost:8000/admin/api_keys
2. Enter a name (e.g. `My app`) and click **Generate Key**
3. Copy the token — it is only shown once

Pass the token in the `Authorization` header with every API request:

```
Authorization: Bearer <your-api-key>
```

In Swagger UI, click **Authorize 🔒**, paste the token, and click **Authorize**. All "Try it out" calls will include the header automatically.
