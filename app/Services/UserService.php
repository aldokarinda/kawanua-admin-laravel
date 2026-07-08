<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getPaginatedUsers($search = null, $status = null, $perPage = 10)
    {
        $query = User::with('roles');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('is_active', $status === 'active');
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function createUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'department' => $data['department'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (isset($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'department' => $data['department'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        } else {
            $user->syncRoles([]);
        }

        return $user;
    }

    public function deleteUser(User $user)
    {
        if ($user->id === 1 || $user->hasRole('super-admin')) {
            return false;
        }

        return $user->delete();
    }

    public function bulkDeleteUsers(array $ids)
    {
        $users = User::whereIn('id', $ids)->get();

        $idsToDelete = $users->filter(function($user) {
            return $user->id !== 1 && !$user->hasRole('super-admin');
        })->pluck('id')->toArray();

        if (count($idsToDelete) > 0) {
            User::whereIn('id', $idsToDelete)->delete();
        }

        return count($idsToDelete) === count($ids); // Return true if all requested were deleted
    }
}
