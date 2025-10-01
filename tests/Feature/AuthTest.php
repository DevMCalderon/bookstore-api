<?php

namespace Tests\Feature; // Use Feature namespace for app-boosting tests

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Auto-resets DB, avoids "email unique" errors in repeats
    
    public function test_user_can_register()
    {
        // Simulate POST to /api/register with JSON data
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        // Check: Status 201 (created), JSON has 'user' and 'token' keys
        // assertJsonStructure(): Verifies response JSON has exact keys (like 'user' object and 'token' string).
        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }
    
    public function test_user_can_login()
    {
        // Create a test user in DB (hashed password)     
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'), // Hash::make encrypts password safely
        ]);
        
        // Simulate POST to /api/login with credentials
        $response = $this->postJson('/api/login', [
           'email' => 'test@example.com',
           'password' => 'password123',
        ]);
        
        // Check: Status 200 (OK), JSON has 'user' and 'token'
        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }
    public function test_login_fails_with_wrong_password()
    {
        // Create a test user in DB (hashed password)     
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'), // Hash::make encrypts password safely
        ]);
        
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong',
        ]);
        
        $response->assertStatus(401); // Unauthorized
    }
}