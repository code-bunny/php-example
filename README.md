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
