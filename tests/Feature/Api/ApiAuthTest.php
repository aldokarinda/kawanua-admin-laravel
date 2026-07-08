<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password'  => bcrypt('Secur3P@ssw0rd!'),
            'is_active' => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function test_user_can_login_and_receive_token_pair(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => $this->user->email,
            'password' => 'Secur3P@ssw0rd!',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'roles'],
                'access_token',
                'refresh_token',
                'token_type',
                'access_expires_in',
                'refresh_expires_in',
            ]);

        $this->assertSame('Bearer', $response->json('token_type'));
        $this->assertEquals(15 * 60, $response->json('access_expires_in'));
    }

    public function test_login_with_invalid_credentials_returns_422(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => $this->user->email,
            'password' => 'wrongpassword',
        ])->assertUnprocessable();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactive = User::factory()->create([
            'password'  => bcrypt('Secur3P@ssw0rd!'),
            'is_active' => false,
        ]);

        $this->postJson('/api/auth/login', [
            'email'    => $inactive->email,
            'password' => 'Secur3P@ssw0rd!',
        ])->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // ME
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_access_me(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        $this->withToken($tokens['access_token'])
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonFragment(['email' => $this->user->email]);
    }

    public function test_unauthenticated_request_to_me_returns_401(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_expired_access_token_cannot_access_me(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        // Manually expire the access token
        $this->user->tokens()
            ->where('name', 'access')
            ->update(['expires_at' => now()->subMinute()]);

        $this->withToken($tokens['access_token'])
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }

    // -------------------------------------------------------------------------
    // REFRESH — Token Rotation
    // -------------------------------------------------------------------------

    public function test_user_can_refresh_tokens(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $tokens['refresh_token'],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'refresh_token',
                'access_expires_in',
            ]);

        // New tokens must be different from the old ones
        $this->assertNotEquals($tokens['access_token'],  $response->json('access_token'));
        $this->assertNotEquals($tokens['refresh_token'], $response->json('refresh_token'));
    }

    public function test_old_access_token_cannot_be_used_after_refresh(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        // Refresh — old tokens get revoked
        $newTokens = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $tokens['refresh_token'],
        ])->json();

        // Old access token must now fail
        $this->withToken($tokens['access_token'])
            ->getJson('/api/auth/me')
            ->assertUnauthorized();

        // New access token must work
        $this->withToken($newTokens['access_token'])
            ->getJson('/api/auth/me')
            ->assertOk();
    }

    public function test_reusing_old_refresh_token_revokes_all_tokens_breach_detection(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        // Perform a valid refresh
        $this->postJson('/api/auth/refresh', [
            'refresh_token' => $tokens['refresh_token'],
        ])->assertOk();

        // Attempt to reuse the OLD refresh token (replay attack)
        $this->postJson('/api/auth/refresh', [
            'refresh_token' => $tokens['refresh_token'],
        ])->assertStatus(401);

        // All user tokens should now be revoked
        $this->assertEquals(0, $this->user->tokens()->count());
    }

    public function test_invalid_refresh_token_returns_401(): void
    {
        $this->postJson('/api/auth/refresh', [
            'refresh_token' => '1|totally_fake_token_string',
        ])->assertUnauthorized();
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_logout(): void
    {
        $service = app(ApiTokenService::class);
        $tokens  = $service->issueTokenPair($this->user);

        $this->withToken($tokens['access_token'])
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJsonFragment(['message' => 'Logged out successfully.']);

        $this->assertEquals(0, $this->user->tokens()->count());
    }

    public function test_logout_without_token_returns_401(): void
    {
        $this->postJson('/api/auth/logout')->assertUnauthorized();
    }
}
