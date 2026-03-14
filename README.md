# php-example

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
