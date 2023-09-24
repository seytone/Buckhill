<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_works(): void
    {
        $this->authenticate('admin@buckhill.co.uk', 'admin')
            ->assertStatus(status: 200);
    }

    public function test_api_login_validates_form_data(): void
    {
        $this->authenticate('loremipsum', '')
            ->assertStatus(status: 400);
    }

    public function test_api_login_validates_credentials(): void
    {
        $this->authenticate('admin@buckhill.co.uk', 'admin123')
            ->assertStatus(status: 401);
    }

    public function test_api_login_returns_success_response(): void
    {
        $this->authenticate('admin@buckhill.co.uk', 'admin')
            ->assertJsonStructure([
                'success',
                'message',
                'authorization' => [
                    'type',
                    'access_token',
                    'expires_at',
                    'user',
                ],
            ])
            ->assertJsonFragment([
                'success' => true,
                'message' => 'User logged-in successfully.',
            ]);
    }

    public function test_api_logout_works(): void
    {
        $login = (object) json_decode($this->authenticate('admin@buckhill.co.uk', 'admin')->getContent());

        $this->withHeaders([
            'Authorization'=>'Bearer ' . $login->authorization->access_token
        ])
            ->get(uri: '/api/v1/auth/logout')
            ->assertStatus(status: 200);
    }

    private function authenticate(string $email, string $password): \Illuminate\Testing\TestResponse
    {
        $this->seed();

        $login = $this->post(uri: '/api/v1/auth/login', data: [
            'email' => $email,
            'password' => $password,
        ]);

        return $login;
    }
}
