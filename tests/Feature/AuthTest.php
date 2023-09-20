<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_works(): void
    {
        $this->seed();

        $this->post(uri: '/api/v1/auth/login', data: [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
        ])
            ->assertStatus(status: 200);
    }

    public function test_api_login_validates_form_data(): void
    {
        $this->seed();

        $this->post(uri: '/api/v1/auth/login', data: [
            'email' => 'loremipsum',
            'password' => '',
        ])
            ->assertStatus(status: 400);
    }

    public function test_api_login_validates_credentials(): void
    {
        $this->seed();

        $this->post(uri: '/api/v1/auth/login', data: [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin123',
        ])
            ->assertStatus(status: 401);
    }

    public function test_api_login_returns_success_response(): void
    {
        $this->seed();

        $this->post(uri: '/api/v1/auth/login', data: [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
        ])
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
        $this->seed();

        $login = (object) json_decode($this->post(uri: '/api/v1/auth/login', data: [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
        ])->getContent());

        $this->withHeaders([
            'Authorization'=>'Bearer ' . $login->authorization->access_token
        ])
            ->get(uri: '/api/v1/auth/logout')
            ->assertStatus(status: 200);
    }
}
