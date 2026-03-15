<?php namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

// Unit tests for the Model base class.
// Uses an in-memory SQLite database — no server or MySQL needed.
class ModelTest extends TestCase
{
    private static \PDO $db;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../helpers/logger.php';
        require_once __DIR__ . '/../../models/Model.php';
        require_once __DIR__ . '/../../models/Post.php';

        self::$db = new \PDO('sqlite::memory:', options: [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        self::$db->exec("
            CREATE TABLE posts (
                id          CHAR(36) PRIMARY KEY,
                title       TEXT NOT NULL,
                body        TEXT NOT NULL,
                created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        \Model::setDb(self::$db);
    }

    protected function tearDown(): void
    {
        self::$db->exec("DELETE FROM posts");
    }

    // ── insert / find ────────────────────────────────────────────────

    public function test_save_inserts_new_record(): void
    {
        $post = new \Post(['title' => 'Hello', 'body' => 'World']);
        $post->save();

        $this->assertNotNull($post->id);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $post->id,
            'id should be a valid UUID v4'
        );
    }

    public function test_find_returns_saved_record(): void
    {
        $post = new \Post(['title' => 'Find Me', 'body' => 'Body']);
        $post->save();

        $found = \Post::find($post->id);

        $this->assertNotNull($found);
        $this->assertSame('Find Me', $found->title);
    }

    public function test_find_returns_null_for_unknown_id(): void
    {
        $this->assertNull(\Post::find('00000000-0000-0000-0000-000000000000'));
    }

    // ── update ───────────────────────────────────────────────────────

    public function test_save_updates_existing_record(): void
    {
        $post = new \Post(['title' => 'Before', 'body' => 'Body']);
        $post->save();

        $post->title = 'After';
        $post->save();

        $this->assertSame('After', \Post::find($post->id)->title);
    }

    // ── delete ───────────────────────────────────────────────────────

    public function test_delete_removes_record(): void
    {
        $post = new \Post(['title' => 'Delete Me', 'body' => 'Body']);
        $post->save();
        $id = $post->id;

        $post->delete();

        $this->assertNull(\Post::find($id));
    }

    // ── all / count / paginate ───────────────────────────────────────

    public function test_all_returns_all_records(): void
    {
        (new \Post(['title' => 'A', 'body' => 'x']))->save();
        (new \Post(['title' => 'B', 'body' => 'x']))->save();

        $this->assertCount(2, \Post::all());
    }

    public function test_count_returns_correct_number(): void
    {
        (new \Post(['title' => 'A', 'body' => 'x']))->save();
        (new \Post(['title' => 'B', 'body' => 'x']))->save();

        $this->assertSame(2, \Post::count());
    }

    public function test_paginate_returns_correct_page(): void
    {
        foreach (range(1, 5) as $i) {
            (new \Post(['title' => "Post $i", 'body' => 'x']))->save();
        }

        $page = \Post::paginate(limit: 2, offset: 0);
        $this->assertCount(2, $page);
    }

    // ── where ────────────────────────────────────────────────────────

    public function test_where_filters_by_column(): void
    {
        (new \Post(['title' => 'Match',    'body' => 'x']))->save();
        (new \Post(['title' => 'No Match', 'body' => 'x']))->save();

        $results = \Post::where('title', 'Match');

        $this->assertCount(1, $results);
        $this->assertSame('Match', $results[0]->title);
    }
}
