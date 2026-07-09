<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    protected $userService;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:user.view', only: ['index']),
            new Middleware('permission:user.create', only: ['create', 'store']),
            new Middleware('permission:user.edit', only: ['edit', 'update']),
            new Middleware('permission:user.delete', only: ['destroy', 'bulkDestroy']),
        ];
    }

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

        $roles = Role::select('id', 'name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::select('id', 'name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        // Handle standard request is_active checkbox vs AJAX JSON boolean
        if ($request->isJson()) {
            $data['is_active'] = (bool) $request->input('is_active');
        } else {
            $data['is_active'] = $request->has('is_active');
        }

        $this->userService->createUser($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::select('id', 'name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
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

    public function export()
    {
        $fileName = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        $users = User::with('roles')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['ID', 'Name', 'Email', 'Department', 'Phone Number', 'Status', 'Roles', 'Created At'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, array(
                    $user->id, 
                    $user->name, 
                    $user->email, 
                    $user->department, 
                    $user->phone_number, 
                    $user->is_active ? 'Active' : 'Inactive', 
                    $user->roles->pluck('name')->implode(', '), 
                    $user->created_at
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
