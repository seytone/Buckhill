<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_admin_listing(): void
    {
        $this->authenticate()
            ->get(uri: '/api/v1/admin')
            ->assertStatus(status: 200);
    }

    public function test_admin_can_get_user_listing(): void
    {
        $this->authenticate()
            ->get(uri: '/api/v1/admin/user-listing')
            ->assertStatus(status: 200);
    }

    public function test_admin_can_edit_user(): void
    {
        $auth = $this->authenticate();

        $user = User::where('is_admin', 0)->inRandomOrder()->first();

        $auth->put(uri: '/api/v1/admin/user-edit/' . $user->uuid, data: [
                'first_name' => 'Lorem',
                'last_name' => 'Ipsum',
                'email' => 'lorem@buckhill.co.uk',
                'address' => 'Lorem Ipsum Dolor',
                'phone_number' => '123456789',
        ])
            ->assertStatus(status: 200);
    }

    public function test_admin_can_delete_user(): void
    {
        $auth = $this->authenticate();

        $user = User::where('is_admin', 0)->inRandomOrder()->first();

        $auth->delete(uri: '/api/v1/admin/user-delete/' . $user->uuid)
            ->assertStatus(status: 200);
    }

    private function authenticate()
    {
        $this->seed();

        $credentials = [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
        ];

        $login = (object) json_decode($this->post(uri: '/api/v1/auth/login', data: $credentials)->getContent());

        return $this->withHeaders([
            'Authorization'=>'Bearer ' . $login->authorization->access_token
        ]);
    }
}
