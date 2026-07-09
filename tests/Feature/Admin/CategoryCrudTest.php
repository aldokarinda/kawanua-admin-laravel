<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        collect(['category.view', 'category.create', 'category.edit', 'category.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($this->superAdminRole);
    }

    public function test_super_admin_can_view_categories_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertViewIs('admin.categories.index');
    }

    public function test_guest_cannot_view_categories_index(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect('/login');
    }

    public function test_super_admin_can_create_category(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic goods',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_super_admin_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Old Tech',
            'slug' => 'old-tech',
            'description' => 'Old technical tools',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'New Tech',
                'slug' => 'new-tech',
                'description' => 'New technical tools',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Tech']);
    }

    public function test_super_admin_can_delete_category(): void
    {
        $category = Category::create([
            'name' => 'Garbage',
            'slug' => 'garbage',
            'description' => 'To be deleted',
        ]);

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
