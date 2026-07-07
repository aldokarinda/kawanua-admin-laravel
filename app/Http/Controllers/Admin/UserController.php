<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'phone_number' => $request->phone_number,
            'is_active' => $request->has('is_active'),
        ]);

        if($request->has('roles')) {
            $user->assignRole($request->roles);
        }

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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'array'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'phone_number' => $request->phone_number,
            'is_active' => $request->has('is_active'),
        ];

        if($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        User::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.users.index')->with('success', 'Selected users deleted successfully.');
    }
}
