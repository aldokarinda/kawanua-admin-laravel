<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Setup permissions
        collect(['user.view', 'user.create', 'user.edit', 'user.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($this->superAdminRole);
    }

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function test_super_admin_can_view_users_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.users.index');
    }

    public function test_guest_cannot_view_users_index(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_view_users_index(): void
    {
        $limited = User::factory()->create();
        $this->actingAs($limited)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // CREATE / STORE
    // -------------------------------------------------------------------------

    public function test_super_admin_can_view_create_user_form(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertViewIs('admin.users.create');
    }

    public function test_super_admin_can_create_user(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name'                  => 'New User',
                'email'                 => 'newuser@example.com',
                'password'              => 'Secur3P@ssw0rd!',
                'password_confirmation' => 'Secur3P@ssw0rd!',
                'is_active'             => true,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_store_user_requires_strong_password(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name'                  => 'Test User',
                'email'                 => 'test@example.com',
                'password'              => 'weakpass',
                'password_confirmation' => 'weakpass',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_store_user_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name'                  => 'Another User',
                'email'                 => 'duplicate@example.com',
                'password'              => 'Secur3P@ssw0rd!',
                'password_confirmation' => 'Secur3P@ssw0rd!',
            ])
            ->assertSessionHasErrors('email');
    }

    // -------------------------------------------------------------------------
    // EDIT / UPDATE
    // -------------------------------------------------------------------------

    public function test_super_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertViewIs('admin.users.edit');
    }

    public function test_super_admin_can_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name'  => 'Updated Name',
                'email' => $user->email,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_super_admin_can_update_user_with_new_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'NewSecur3P@ssw0rd!',
                'password_confirmation' => 'NewSecur3P@ssw0rd!',
            ])
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_update_user_rejects_weak_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->put(route('admin.users.update', $user), [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => 'weak',
                'password_confirmation' => 'weak',
            ])
            ->assertSessionHasErrors('password');
    }

    // -------------------------------------------------------------------------
    // DESTROY / BULK DESTROY
    // -------------------------------------------------------------------------

    public function test_super_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_super_admin_can_bulk_delete_users(): void
    {
        $users = User::factory()->count(3)->create();
        $ids = $users->pluck('id')->toArray();

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.bulk-destroy'), ['ids' => $ids])
            ->assertRedirect(route('admin.users.index'));

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('users', ['id' => $id]);
        }
    }
}
