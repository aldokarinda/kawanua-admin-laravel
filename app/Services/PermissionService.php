<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
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

    public function createPermission(array $data)
    {
        return Permission::create(['name' => $data['name']]);
    }

    public function updatePermission(Permission $permission, array $data)
    {
        return $permission->update(['name' => $data['name']]);
    }

    public function deletePermission(Permission $permission)
    {
        return $permission->delete();
    }
}
