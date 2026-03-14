<?php namespace Tests\Pages\Admin;

class ApiKeysTest extends \AdminTestCase
{
    public function test_index_returns_ok(): void
    {
        $this->get('/admin/api_keys')->assertOk();
    }

    public function test_create_api_key_redirects(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/api_keys');

        $this->post('/admin/api_keys', [
            'csrf_token' => $csrf,
            'name'       => 'Test Key ' . uniqid(),
        ])->assertRedirect();
    }

    public function test_create_without_name_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/api_keys');

        $this->post('/admin/api_keys', [
            'csrf_token' => $csrf,
            'name'       => '',
        ])->assertStatus(422);
    }

    public function test_delete_api_key_redirects(): void
    {
        // Create a key, find its id from the page, then delete it
        $csrf = $this->fetchCsrfToken('/admin/api_keys');
        $this->post('/admin/api_keys', [
            'csrf_token' => $csrf,
            'name'       => 'Temp Key ' . uniqid(),
        ]);

        $body = $this->get('/admin/api_keys')->body;
        preg_match_all('#/admin/api_keys/([0-9a-f-]{36})/delete#', $body, $matches);
        $id = end($matches[1]); // last one is the newest

        $csrf = $this->fetchCsrfToken('/admin/api_keys');
        $this->post("/admin/api_keys/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
