<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        collect(['menu.view', 'menu.create', 'menu.edit', 'menu.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($superAdminRole);
    }

    public function test_super_admin_can_view_menus_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.menus.index'))
            ->assertOk()
            ->assertViewIs('admin.menus.index');
    }

    public function test_guest_cannot_view_menus_index(): void
    {
        $this->get(route('admin.menus.index'))->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_view_menus(): void
    {
        $limited = User::factory()->create();
        $this->actingAs($limited)
            ->get(route('admin.menus.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_create_menu(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.menus.store'), [
                'name'       => 'Test Menu',
                'slug'       => 'test-menu',
                'order'      => 1,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseHas('menus', ['slug' => 'test-menu']);
    }

    public function test_store_menu_requires_unique_slug(): void
    {
        Menu::create(['name' => 'Existing', 'slug' => 'existing-menu', 'order' => 1]);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.menus.store'), [
                'name'  => 'Another',
                'slug'  => 'existing-menu',
                'order' => 2,
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_super_admin_can_update_menu(): void
    {
        $menu = Menu::create(['name' => 'Old', 'slug' => 'old-slug', 'order' => 1]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.menus.update', $menu), [
                'name'  => 'Updated Menu',
                'slug'  => 'old-slug',
                'order' => 1,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Updated Menu']);
    }

    public function test_super_admin_can_delete_menu(): void
    {
        $menu = Menu::create(['name' => 'Delete Me', 'slug' => 'delete-me', 'order' => 1]);

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.menus.destroy', $menu))
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }
}
