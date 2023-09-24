<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_his_own_profile(): void
    {
        $auth = $this->authenticate();

        $auth->request->get(uri: '/api/v1/user/' . $auth->user->uuid)
            ->assertStatus(status: 200);
    }

    public function test_user_cannot_see_external_profile(): void
    {
        $auth = $this->authenticate();

        $user = User::where('uid', '!=', $auth->user->uuid)->where('is_admin', 0)->inRandomOrder()->first();

        $auth->request->get(uri: '/api/v1/user/' . $user->uuid)
            ->assertStatus(status: 405);
    }

    public function test_user_can_edit_his_own_profile(): void
    {
        $auth = $this->authenticate();

        $auth->request->put(uri: '/api/v1/user/' . $auth->user->uuid, data: [
                'first_name' => 'Lorem',
                'last_name' => 'Ipsum',
                'email' => 'lorem@buckhill.co.uk',
                'address' => 'Lorem Ipsum Dolor',
                'phone_number' => '123456789',
        ])
            ->assertStatus(status: 200);
    }

    public function test_user_can_get_his_order_listing(): void
    {
        $auth = $this->authenticate();

        $auth->request->get(uri: '/api/v1/user/orders/' . $auth->user->uuid)
            ->assertStatus(status: 200);
    }

    private function authenticate()
    {
        $this->seed();

        $credentials = [
            'email' => 'user@buckhill.co.uk',
            'password' => 'users',
        ];

        $login = (object) json_decode($this->post(uri: '/api/v1/auth/login', data: $credentials)->getContent());

        $request = $this->withHeaders([
            'Authorization'=>'Bearer ' . $login->authorization->access_token
        ]);

        return (object) [
            'request' => $request,
            'user' => $login->authorization->user
        ];
    }
}
