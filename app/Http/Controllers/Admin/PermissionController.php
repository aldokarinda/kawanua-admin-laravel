<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    protected $permissionService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:permission.view', only: ['index']),
            new Middleware('permission:permission.create', only: ['create', 'store']),
            new Middleware('permission:permission.edit', only: ['edit', 'update']),
            new Middleware('permission:permission.delete', only: ['destroy']),
        ];
    }

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        $groupedPermissions = $this->permissionService->getGroupedPermissions();

        return view('admin.permissions.index', compact('groupedPermissions', 'permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $this->permissionService->createPermission($data);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name,'.$permission->id
        ]);

        $this->permissionService->updatePermission($permission, $data);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $this->permissionService->deletePermission($permission);
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
