<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TagCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        collect(['tag.view', 'tag.create', 'tag.edit', 'tag.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($this->superAdminRole);
    }

    public function test_super_admin_can_view_tags_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.tags.index'))
            ->assertOk()
            ->assertViewIs('admin.tags.index');
    }

    public function test_guest_cannot_view_tags_index(): void
    {
        $this->get(route('admin.tags.index'))->assertRedirect('/login');
    }

    public function test_super_admin_can_create_tag(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.tags.store'), [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'PHP Framework',
            ])
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
    }

    public function test_super_admin_can_update_tag(): void
    {
        $tag = Tag::create([
            'name' => 'Vue',
            'slug' => 'vue',
            'description' => 'JS framework',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.tags.update', $tag), [
                'name' => 'Vue 3',
                'slug' => 'vue-3',
                'description' => 'JS framework upgraded',
            ])
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Vue 3']);
    }

    public function test_super_admin_can_delete_tag(): void
    {
        $tag = Tag::create([
            'name' => 'Garbage Tag',
            'slug' => 'garbage-tag',
            'description' => 'To be deleted',
        ]);

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.tags.destroy', $tag))
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
