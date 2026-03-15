# php-app

A PHP web application with a JSON:API, an admin panel, and a public site. Built to demonstrate how a well-structured PHP app works without a framework.

This is intended for a student who is learning PHP to build up their own system, with common gotchas covered.

If you want to build something simpler to start from, check out the [`basic-example`](../../tree/basic-example) branch, which contains just the first commit — a minimal working app with the core building blocks in place.

If you want to go further, use one of the off-the-shelf frameworks. Ideally Ruby on Rails, but if PHP is your cuppa tea, Laravel is the way to go.

## Application Design

The application is built to demonstrate a few different patterns without leaning on a framework:

- **MVC** — HTML pages follow a controller/view split. Controllers handle logic and pass data to views; views are pure templates. The router in `index.php` owns layout rendering.
- **ActiveRecord** — Models extend a base `Model` class with `find`, `where`, `save`, `delete`, `paginate`, and `count`. Each model is a thin subclass that declares its table name.
- **API DSL** — The JSON:API layer uses a Grape-inspired DSL (`resource`, `routeParam`, `get`, `post`, `patch`, `delete`) with a path stack for nested routes. No classes or `$this` in endpoint files.
- **Serializers** — JSON:API responses are built by serializer classes that declare attributes and relationships. Attribute names are camelCase; transforms are defined as methods.
- **Tests** — PHPUnit with two kinds of tests: unit tests that load app code in-process, and request tests that hit a real HTTP server.

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

Run migrations (also writes `db/schema.php`):

```bash
bin/migrate
```

Seed the database with sample data and the first admin user:

```bash
php db/seeds.php
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

## Generators

`bin/generate` scaffolds boilerplate so you can focus on the logic. Generated `.php` files are automatically linted with `php -l` and any syntax errors are printed immediately.

### Model

Creates a model class and a numbered migration:

```bash
bin/generate model Post title:string body:text
```

Creates:
- `app/models/Post.php` — thin `Model` subclass
- `migrations/NNNN_create_posts.php` — `CREATE TABLE` with `id`, your fields, `created_at`, `updated_at`

Field types: `string` (VARCHAR 255), `text`, `integer`, `boolean`, `decimal`, `float`, `date`, `datetime`.

### Controller

Creates a controller and a view stub per action, and injects the routes into `index.php`:

```bash
bin/generate controller Posts index show create edit
```

Creates:
- `app/controllers/PostsController.php` — stub method per action
- `app/views/posts/{action}.php` — one view stub per action

Injects into `index.php`:

| Action | Route added |
|--------|-------------|
| `index` | `case '/posts':` → `$ctrl->index()` |
| `create` | `case '/posts/new':` → `$ctrl->create()` |
| `show` | `preg_match('#^/posts/([uuid])$#')` → `$ctrl->show($id)` |
| `edit` | `preg_match('#^/posts/([uuid])/edit$#')` → `$ctrl->edit($id)` |

Other action names are created as stubs but not wired — add those routes manually.

### API endpoint

Creates an endpoint file with full CRUD, a serializer, and registers both in `app/api/Api.php`:

```bash
bin/generate api Post title:string body:text
```

Creates:
- `app/api/endpoints/Posts.php` — `resource()` block with `get`, `post`, `patch`, `delete`
- `app/api/serializers/PostSerializer.php` — serializer with your fields + `createdAt`/`updatedAt`
- Updates `app/api/Api.php` to require the new endpoint

The post/patch handlers include a `// TODO: validate $attrs` comment — add your own validation there.

### Serializer

Creates just the serializer:

```bash
bin/generate serializer Post title:string body:text
```

### Migration

Creates a standalone migration. The name determines what SQL is generated:

```bash
bin/generate migration AddFullBodyToUsers full_body:text   # ALTER TABLE users ADD COLUMN
bin/generate migration RemoveAvatarFromPosts avatar        # ALTER TABLE posts DROP COLUMN
bin/generate migration AddIndexToSubscribers               # empty migration with TODO
```

| Name pattern | Generated SQL |
|---|---|
| `Add<X>To<Table>` + fields | `ALTER TABLE <table> ADD COLUMN ...` |
| `Remove<X>From<Table>` + fields | `ALTER TABLE <table> DROP COLUMN ...` |
| Anything else | Empty migration with a `// TODO` comment |

The table name is taken directly from the `To<Table>` / `From<Table>` suffix — `ToApiKeys` → `api_keys`.

After generating a migration, run it:

```bash
bin/migrate
```

### Notes

- **Won't overwrite** — skips files that already exist, safe to re-run
- **Route deduplication** — controller routes are not added twice if they're already in `index.php`
- **Migration numbering** — auto-increments from the highest existing number

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

Run migrations against both (regenerates `db/schema.php` each time):

```bash
bin/migrate
APP_ENV=test bin/migrate
```

## Project structure

```
├── index.php              # Front controller — bootstrap, middleware, API dispatch
├── config/
│   ├── database.php       # Database connection (PDO)
│   ├── routes.php         # URL → controller routing (HTML + admin)
│   ├── openapi.yaml       # OpenAPI specification (served at /openapi.yaml)
│   └── phpunit.xml        # PHPUnit configuration
├── db/
│   ├── schema.php         # Auto-generated schema snapshot (committed to git)
│   ├── seeds.php          # Seeds sample data and the first admin user
│   └── migrations/        # Versioned schema migrations
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
├── tests/                 # PHPUnit test suite
│   ├── unit/              # In-process unit tests
│   ├── api/v1/            # Request tests for the JSON:API
│   └── pages/             # Request tests for HTML pages (including admin)
├── bin/
│   ├── serve              # Start the dev server (localhost:8000)
│   ├── router.php         # PHP built-in server router (used by bin/serve and tests)
│   ├── migrate            # Run pending migrations
│   ├── console            # Interactive shell (PsySH)
│   ├── test               # Run the test suite
│   ├── coverage           # Run tests and generate an HTML coverage report
│   └── generate           # Scaffold models, controllers, API endpoints, serializers, migrations
└── docker/
    └── init.sql           # Creates mydb_development and mydb_test databases
```
