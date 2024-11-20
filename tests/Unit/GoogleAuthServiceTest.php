<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\GoogleAuthService;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class GoogleAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_google_user_with_valid_gmail()
    {
        $service = new GoogleAuthService();
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('test@gmail.com');
        $googleUser->shouldReceive('getId')->andReturn('google-id');
        $googleUser->shouldReceive('getName')->andReturn('Test User');
        $user = $service->handleGoogleUser($googleUser);
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'google_id' => 'google-id',
            'name' => 'Test User',
        ]);
        $existingUser = $service->handleGoogleUser($googleUser);
        $this->assertEquals($user->id, $existingUser->id);
    }

    public function test_handle_google_user_with_non_gmail_account()
    {
        $service = new GoogleAuthService();
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $googleUser->shouldReceive('getId')->andReturn('google-id');
        $googleUser->shouldReceive('getName')->andReturn('Test User');
        $response = $service->handleGoogleUser($googleUser);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Only Gmail accounts are allowed'], $response->original);
    }
}
