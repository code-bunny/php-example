# php-app

A PHP web application with a JSON:API, an admin panel, and a public site. Built to demonstrate how a well-structured PHP app works without a framework.

This is intended for a student who is learning PHP to build up their own system, with common gotchas covered.

If you want to build something simpler to start from, go back to the first commit in the repository where the first building blocks are demonstrated.

If you want to go further, use one of the off-the-shelf frameworks. Ideally Ruby on Rails, but if PHP is your cuppa tea, Laravel is the way to go.

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

## JSON:API

The API follows the [JSON:API v1.1](https://jsonapi.org/) spec. All attribute names use camelCase. Responses include a `jsonapi` version envelope.

### Routing DSL

Endpoints are defined in `app/api/endpoints/` using a Grape-inspired DSL. Routes are grouped by resource — `resource()` sets the path prefix, `routeParam()` adds a URL parameter segment, and `get()` / `post()` / `patch()` / `delete()` register handlers scoped to the current path.

```php
resource('posts', function () {

    get(function () { ... });          // GET /api/v1/posts
    post(function () { ... });         // POST /api/v1/posts
    get('latest', function () { ... }); // GET /api/v1/posts/latest

    routeParam(':id', function () {
        get(function () { ... });             // GET /api/v1/posts/:id
        patch(function () { ... });           // PATCH /api/v1/posts/:id
        delete(function () { ... });          // DELETE /api/v1/posts/:id
        patch('publish', function () { ... }); // PATCH /api/v1/posts/:id/publish
    });

});
```

Passing a path string as the first argument to any HTTP method appends that segment to the current context. This works at any nesting level — inside a `resource()` block, inside a `routeParam()` block, or both.

A real example — `GET /api/v1/posts/latest` returns the 10 most recent posts:

```php
get('latest', function () {
    return ['data' => PostSerializer::many(Post::paginate(10, 0))];
});
```

Inside any handler, these helpers are available:

| Helper | Description |
|--------|-------------|
| `param('id')` | URL parameter (e.g. the `:id` segment) |
| `attributes()` | Parsed `data.attributes` from the request body |
| `pageParams()` | `page[size]` and `page[number]` from the query string |
| `paginationLinks($base, $number, $last, $size)` | Builds JSON:API pagination links |
| `notFound($message)` | Responds 404 and exits |
| `unprocessable($errors)` | Responds 422 and exits |
| `conflict($message)` | Responds 409 and exits |

Handlers return an array (status 200), `[$data, $status]` for other codes, or `null` for 204 No Content.

To add a new endpoint, create a file in `app/api/endpoints/` and require it in `app/api/Api.php`.

### Serializers

Serializers live in `app/api/serializers/` and extend `Serializer`. Each one declares its JSON:API type, which attributes to expose, and optionally relationships. Attribute names are camelCase — they are automatically mapped to snake_case model properties (`createdAt` → `created_at`). `createdAt` and `updatedAt` are formatted as ISO 8601 by the base class.

```php
class PostSerializer extends Serializer
{
    protected string $type = 'posts';

    protected static array $attributes = ['title', 'body', 'createdAt', 'updatedAt'];
}
```

Use a protected method with the same name as the attribute to apply a custom transform:

```php
class PostSerializer extends Serializer
{
    protected string $type = 'posts';

    protected static array $attributes = ['title', 'body', 'createdAt', 'updatedAt'];

    protected function title(object $post): string
    {
        return strtoupper($post->title);
    }
}
```

Relationships produce [resource linkage](https://jsonapi.org/format/#document-resource-object-linkage) (`id` + `type` only — no `included`):

```php
class PostSerializer extends Serializer
{
    protected string $type = 'posts';

    protected static array $attributes  = ['title', 'body'];
    protected static array $belongsTo   = ['author' => UserSerializer::class];
    protected static array $hasMany     = ['comments' => CommentSerializer::class];
}
```

Call serializers from endpoint handlers:

```php
PostSerializer::one($post)      // single resource object
PostSerializer::many($posts)    // array of resource objects
```

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
├── index.php              # Front controller — all requests go through here
├── router.php             # PHP built-in server router
├── migrate.php            # Runs pending migrations
├── seeds.php              # Seeds sample data and the first admin user
├── db.php                 # Database connection
├── app/
│   ├── api/               # JSON:API layer
│   │   ├── Route.php      # Static router + DSL global functions
│   │   ├── Api.php        # Loads all endpoint files
│   │   ├── endpoints/     # One file per resource (Posts, Contacts, Subscribers)
│   │   └── serializers/   # JSON:API serializers
│   ├── controllers/       # HTML controllers (public + admin)
│   │   └── admin/
│   ├── models/            # Model classes (Post, Contact, Subscriber, ApiKey, User)
│   └── views/
│       ├── layouts/       # public.php and admin.php layouts
│       ├── components/    # Reusable view helpers (icons, pagination, forms…)
│       ├── shared/        # _header.php, _footer.php
│       ├── admin/         # Admin panel views
│       └── …              # Public views (home, blog, contact, posts…)
├── lib/                   # Framework-level utilities (csrf, flash, auth, rate limiting…)
├── migrations/            # Versioned schema migrations
├── tests/                 # PHPUnit test suite
│   ├── unit/              # In-process unit tests
│   ├── api/v1/            # Request tests for the JSON:API
│   └── pages/             # Request tests for HTML pages (including admin)
├── bin/
│   ├── serve              # Start the dev server (localhost:8000)
│   ├── console            # Interactive shell (PsySH)
│   ├── test               # Run the test suite
│   └── coverage           # Run tests and generate an HTML coverage report
└── docker/
    └── init.sql           # Creates mydb_development and mydb_test databases
```
