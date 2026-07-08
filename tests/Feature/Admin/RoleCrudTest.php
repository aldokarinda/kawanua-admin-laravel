<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        collect(['role.view', 'role.create', 'role.edit', 'role.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($this->superAdminRole);
    }

    public function test_super_admin_can_view_roles_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertViewIs('admin.roles.index');
    }

    public function test_guest_cannot_view_roles_index(): void
    {
        $this->get(route('admin.roles.index'))->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_view_roles(): void
    {
        $limited = User::factory()->create();
        $this->actingAs($limited)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_create_role(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), [
                'name'        => 'Editor',
                'description' => 'Can edit content',
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    public function test_store_role_requires_unique_name(): void
    {
        Role::firstOrCreate(['name' => 'Duplicate Role']);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.store'), ['name' => 'Duplicate Role'])
            ->assertSessionHasErrors('name');
    }

    public function test_super_admin_can_update_role(): void
    {
        $role = Role::firstOrCreate(['name' => 'Old Role Name']);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.roles.update', $role), ['name' => 'New Role Name'])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'New Role Name']);
    }

    public function test_super_admin_can_delete_non_system_role(): void
    {
        $role = Role::firstOrCreate(['name' => 'Deletable Role']);

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_super_admin_cannot_delete_super_admin_role(): void
    {
        $this->actingAs($this->superAdmin)
            ->delete(route('admin.roles.destroy', $this->superAdminRole))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');
    }

    public function test_super_admin_can_clone_role(): void
    {
        $role = Role::firstOrCreate(['name' => 'Original Role']);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.roles.clone', $role))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'Original Role (Copy)']);
    }
}
