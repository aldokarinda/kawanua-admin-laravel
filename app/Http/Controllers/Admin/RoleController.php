<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    protected $roleService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:role.view', only: ['index']),
            new Middleware('permission:role.create', only: ['create', 'store']),
            new Middleware('permission:role.edit', only: ['edit', 'update']),
            new Middleware('permission:role.delete', only: ['destroy', 'clone']),
        ];
    }

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getPaginatedRoles();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();
        $groupedPermissions = $this->roleService->getGroupedPermissions();

        return view('admin.roles.create', compact('groupedPermissions', 'permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->roleService->createRole($request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->roleService->getAllPermissions();
        $groupedPermissions = $this->roleService->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (!$this->roleService->updateRole($role, $request->validated())) {
            return redirect()->back()->with('error', 'Cannot change the name of the super-admin role.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!$this->roleService->deleteRole($role)) {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function clone(Role $role)
    {
        $this->roleService->cloneRole($role);
        return redirect()->route('admin.roles.index')->with('success', 'Role cloned successfully.');
    }
}
