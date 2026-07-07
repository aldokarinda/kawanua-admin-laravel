<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getPaginatedUsers(
            $request->search,
            $request->status
        )->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'array'
        ]);

        $data['is_active'] = $request->has('is_active');
        
        $this->userService->createUser($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'array'
        ]);

        $data['is_active'] = $request->has('is_active');

        $this->userService->updateUser($user, $data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!$this->userService->deleteUser($user)) {
            return redirect()->route('admin.users.index')->with('error', 'Super Admin user cannot be deleted.');
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        $allDeleted = $this->userService->bulkDeleteUsers($request->ids);

        if (!$allDeleted) {
            return redirect()->route('admin.users.index')->with('warning', 'Some users were deleted, but Super Admin users were skipped.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Selected users deleted successfully.');
    }
}
