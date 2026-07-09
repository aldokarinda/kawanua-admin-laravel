<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegionCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        collect(['region.view', 'region.create', 'region.edit', 'region.delete'])->each(
            fn($p) => Permission::firstOrCreate(['name' => $p])
        );

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($this->superAdminRole);
    }

    public function test_super_admin_can_view_regions_index(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.regions.index'))
            ->assertOk()
            ->assertViewIs('admin.regions.index');
    }

    public function test_guest_cannot_view_regions_index(): void
    {
        $this->get(route('admin.regions.index'))->assertRedirect('/login');
    }

    public function test_super_admin_can_create_region(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.regions.store'), [
                'name' => 'North America',
                'type' => 'province',
            ])
            ->assertRedirect(route('admin.regions.index'));

        $this->assertDatabaseHas('regions', ['name' => 'North America', 'type' => 'province']);
    }

    public function test_super_admin_can_update_region(): void
    {
        $region = Region::create([
            'name' => 'Europe Old',
            'type' => 'province',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('admin.regions.update', $region), [
                'name' => 'Europe',
                'type' => 'city',
            ])
            ->assertRedirect(route('admin.regions.index'));

        $this->assertDatabaseHas('regions', ['id' => $region->id, 'name' => 'Europe', 'type' => 'city']);
    }

    public function test_super_admin_can_delete_region(): void
    {
        $region = Region::create([
            'name' => 'Temporary Region',
            'type' => 'province',
        ]);

        $this->actingAs($this->superAdmin)
            ->delete(route('admin.regions.destroy', $region))
            ->assertRedirect(route('admin.regions.index'));

        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }
}
