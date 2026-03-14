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

Start the dev server:

```bash
php -S localhost:8000
```

The app is at http://localhost:8000. phpMyAdmin is at http://localhost:8080.
