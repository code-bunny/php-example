# php-example

A minimal PHP web app with no framework — just routing, a database, models, and pages. This is the starting point. Read through it, run it, then build on it.

The [`main`](../../tree/main) branch shows where things go once the app grows: MVC separation, an admin panel, a JSON:API, authentication, tests, and generators. Start here, look there when you need ideas.

## What's here

```
├── index.php                   # Front controller — routing lives here
├── layout.php                  # Shared HTML wrapper (nav, <head>, Tailwind)
├── pages/
│   ├── home.php                # GET /
│   ├── about.php               # GET /about
│   ├── contact.php             # GET /contact
│   └── posts/
│       └── new.php             # GET /posts/new  +  POST /posts/new
├── models/
│   ├── Model.php               # Base class — all(), find(), where(), save(), delete()
│   └── Post.php                # Post model  (table: posts)
├── migrations/
│   └── 0001_create_posts.php   # Creates the posts table
├── migrate.php                 # Migration runner
└── docker-compose.yml          # MySQL + phpMyAdmin
```

## Setup

```bash
docker compose up -d
php migrate.php
```

## Running

```bash
php -S localhost:8000
```

The app is at http://localhost:8000. phpMyAdmin is at http://localhost:8080.

## How it works

### Routing

`index.php` is the entry point for every request. It matches the URL path and loads the right page file:

```php
switch ($path) {
    case '/':      require 'pages/home.php';      break;
    case '/about': require 'pages/about.php';     break;
    // …
}
```

Page files can contain both logic and HTML. For dynamic segments like `/posts/123`, use a regex match instead:

```php
if (preg_match('#^/posts/(\d+)$#', $path, $m)) {
    $id = $m[1];
    require 'pages/posts/show.php';
}
```

After the page file runs, `layout.php` wraps its output. The page sets `$title` and `$content` is captured automatically with output buffering.

### Models

`models/Model.php` is a small ActiveRecord base class. Subclasses declare their table name and get these methods for free:

| Method | What it does |
|---|---|
| `Post::all()` | All rows, as Post objects |
| `Post::find($id)` | One row by id, or null |
| `Post::where('col', $val)` | All rows where column matches |
| `$post->save()` | INSERT (no id) or UPDATE (has id) |
| `$post->delete()` | DELETE by id |

Creating a new model:

```php
// models/Comment.php
require_once __DIR__ . '/Model.php';

class Comment extends Model {
    protected static string $table = 'comments';
}
```

Using it in a page:

```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=mydb', 'user', 'pass');
Model::setDb($pdo);

$post    = Post::find((int) $id);
$comment = new Comment(['post_id' => $post->id, 'body' => $_POST['body']]);
$comment->save();
```

Read a property with `$post->title`. Set one with `$post->title = 'New title'`, then call `$post->save()`.

### Migrations

Each migration is a PHP file in `migrations/` that defines an `up(PDO $pdo)` function:

```php
function up(PDO $pdo): void {
    $pdo->exec("CREATE TABLE comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        body TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
```

Run pending migrations:

```bash
php migrate.php
```

`migrate.php` keeps a `migrations` table so it only runs each file once.

### Layout

`layout.php` is the shared HTML shell. Every page gets the same `<head>`, nav, and `<main>` wrapper. A page sets `$title` and renders its HTML — `index.php` captures the output and passes it to the layout as `$content`.

To change the nav or add a footer, edit `layout.php`.

## What to build next

Some natural next steps:

- **Show a single post** — add `pages/posts/show.php` and a regex route for `/posts/:id`
- **Edit and delete** — add `pages/posts/edit.php`, handle `POST /posts/:id/edit` and `POST /posts/:id/delete`
- **A second model** — create `models/Comment.php`, a migration, and a page that saves comments
- **Validation helper** — extract the `$errors` pattern from `pages/posts/new.php` into a reusable function
- **Database config** — move the PDO credentials into a separate `config.php` so you don't repeat them in every page

The `main` branch handles all of these and more — worth reading once you've outgrown this structure.
