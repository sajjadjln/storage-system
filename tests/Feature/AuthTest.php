<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function user_can_register_successfully()
    {
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'username' => 'testuser'
        ];

        $response = $this->postJson('/api/register', $userData);



        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token',
                    'token_type'
                ],
                'success',
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'email' => 'test@example.com',
                        'name' => 'testuser'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'testuser'
        ]);
    }

    /** @test */
    public function registration_fails_with_duplicate_email()
    {
        $existingUser = [
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'username' => 'existinguser'
        ];

        $this->postJson('/api/register', $existingUser);

        $response = $this->postJson('/api/register', $existingUser);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Email already registered'
            ]);
    }

    /** @test */
    public function user_can_login_successfully()
    {

        $userData = [
            'email' => 'login@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'username' => 'loginuser'
        ];

        $this->postJson('/api/register', $userData);

        $loginData = [
            'email' => 'login@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $loginData);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ],
                    'token',
                    'token_type'
                ],
                'success',
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'login successful'
            ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'the provided credentials are wrong'
            ]);
    }
}
