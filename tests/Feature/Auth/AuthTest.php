<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{postJson};

uses(RefreshDatabase::class);

describe('Authentication', function () {
    it('can register a user', function () {
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'SenhaSegura123!',
            'password_confirmation' => 'SenhaSegura123!',
        ];

        postJson(route('auth.register'), $data)
            ->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    });

    it('can login a user', function () {
        $user = User::factory()->create(['password' => bcrypt('SenhaSegura123!')]);

        $data = [
            'email'    => $user->email,
            'password' => 'SenhaSegura123!',
        ];

        postJson(route('auth.login'), $data)
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
    });

    it('can logout a user', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = postJson(route('auth.logout'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    });
});
