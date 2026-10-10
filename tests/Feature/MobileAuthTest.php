<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_from_mobile(): void
    {
        $user = User::factory()->create([
            'email' => 'mobile@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'Password123!',
            'device_name' => 'iPhone 17 Simulator',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
                'token_type',
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'iPhone 17 Simulator',
        ]);
    }

    public function test_mobile_login_rejects_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'mobile@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'WrongPassword!',
            'device_name' => 'iPhone 17 Simulator',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'iPhone 17 Simulator',
        ]);
    }
}