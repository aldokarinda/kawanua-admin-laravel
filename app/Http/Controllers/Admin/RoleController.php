<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // Spatie roles use users() relation if using proper trait
        $roles = Role::withCount(['users', 'permissions'])->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        // Group permissions by prefix (e.g. user.view -> user)
        $groupedPermissions = $permissions->groupBy(function($item) {
            return explode('.', $item->name)[0] ?? 'general';
        });

        return view('admin.roles.create', compact('groupedPermissions', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        
        if($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function($item) {
            return explode('.', $item->name)[0] ?? 'general';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        
        if($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function clone(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . ' (Copy)',
            'description' => $role->description
        ]);

        $newRole->syncPermissions($role->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role cloned successfully.');
    }
}
