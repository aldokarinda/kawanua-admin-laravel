<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset cached roles and permissions
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_user_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_user_management(): void
    {
        // Seed permissions and roles
        $viewUserPermission = Permission::create(['name' => 'user.view']);
        $user = User::factory()->create();
        $user->givePermissionTo($viewUserPermission);

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_non_super_admin_cannot_access_security_center(): void
    {
        $user = User::factory()->create();

        // Create standard admin role
        $adminRole = Role::create(['name' => 'Admin']);
        $user->assignRole($adminRole);

        $response = $this->actingAs($user)->get('/admin/security');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_security_center(): void
    {
        $user = User::factory()->create();

        // Create Super Admin role
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $user->assignRole($superAdminRole);

        $response = $this->actingAs($user)->get('/admin/security');
        $response->assertStatus(200);
    }

    public function test_user_has_security_relationships(): void
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->twoFactorAuth());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->loginHistories());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->auditLogs());
    }

    public function test_simple_totp_generation_and_verification(): void
    {
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ'; // standard base32 test secret
        $totp = \App\Services\SimpleTOTP::create($secret);
        
        $uri = $totp->getProvisioningUri();
        $this->assertStringContainsString('otpauth://totp/', $uri);
        $this->assertStringContainsString('secret=GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', $uri);

        // Standard verification window check
        $this->assertTrue($totp->verify($this->calculateTotpForTesting($secret, time())));
    }

    private function calculateTotpForTesting(string $secret, int $timestamp): string
    {
        $secretKey = \ParagonIE\ConstantTime\Base32::decodeUpper($secret);
        $timeStep = floor($timestamp / 30);
        $timeBin = pack('N*', 0, $timeStep);
        $hashBin = hash_hmac('sha1', $timeBin, $secretKey, true);
        $offset = ord($hashBin[19]) & 0xf;
        $truncatedHash = substr($hashBin, $offset, 4);
        $num = unpack('N', $truncatedHash)[1] & 0x7fffffff;
        $code = $num % 1000000;
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }
}
