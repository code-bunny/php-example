<?php namespace Tests\Pages\Admin;

class PostsTest extends \AdminTestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->apiDelete("/api/v1/posts/$id");
        }
        parent::tearDown();
    }

    public function test_index_returns_ok(): void
    {
        $this->get('/admin/posts')->assertOk();
    }

    public function test_new_form_returns_ok(): void
    {
        $this->get('/admin/posts/new')->assertOk();
    }

    public function test_create_post_redirects(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/posts/new');
        $id   = null;

        $this->post('/admin/posts/new', [
            'csrf_token' => $csrf,
            'title'      => 'Admin Created Post ' . uniqid(),
            'body'       => 'Some content here',
        ])->assertRedirect();
    }

    public function test_create_without_title_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/posts/new');

        $this->post('/admin/posts/new', [
            'csrf_token' => $csrf,
            'title'      => '',
            'body'       => 'Body without a title',
        ])->assertStatus(422);
    }

    public function test_create_with_duplicate_title_returns_422(): void
    {
        $title = 'Duplicate Title ' . uniqid();
        $id    = $this->apiCreatePost($title);
        $this->created[] = $id;

        $csrf = $this->fetchCsrfToken('/admin/posts/new');

        $this->post('/admin/posts/new', [
            'csrf_token' => $csrf,
            'title'      => $title,
            'body'       => 'Different body',
        ])->assertStatus(422)->assertSee('already exists');
    }

    public function test_edit_form_returns_ok(): void
    {
        $id = $this->apiCreatePost('Edit Form Test ' . uniqid());
        $this->created[] = $id;

        $this->get("/admin/posts/$id/edit")->assertOk();
    }

    public function test_update_post_redirects(): void
    {
        $id = $this->apiCreatePost('Before ' . uniqid());
        $this->created[] = $id;

        $csrf = $this->fetchCsrfToken("/admin/posts/$id/edit");

        $this->post("/admin/posts/$id/edit", [
            'csrf_token' => $csrf,
            'title'      => 'After ' . uniqid(),
            'body'       => 'Updated body',
        ])->assertRedirect();
    }

    public function test_delete_post_redirects(): void
    {
        $id = $this->apiCreatePost('To Delete ' . uniqid());

        $csrf = $this->fetchCsrfToken('/admin/posts');

        $this->post("/admin/posts/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
