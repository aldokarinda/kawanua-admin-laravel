<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleService
{
    public function getPaginatedRoles($perPage = 10)
    {
        return Role::withCount(['users', 'permissions'])->paginate($perPage);
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function getGroupedPermissions()
    {
        return $this->getAllPermissions()->groupBy(function($item) {
            return explode('.', $item->name)[0] ?? 'general';
        });
    }

    public function createRole(array $data)
    {
        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function updateRole(Role $role, array $data)
    {
        if ($role->name === 'super-admin' && $data['name'] !== 'super-admin') {
            return false;
        }

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return true;
    }

    public function deleteRole(Role $role)
    {
        if ($role->name === 'super-admin') {
            return false;
        }

        return $role->delete();
    }

    public function cloneRole(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . ' (Copy)',
            'description' => $role->description
        ]);

        $newRole->syncPermissions($role->permissions);

        return $newRole;
    }
}
