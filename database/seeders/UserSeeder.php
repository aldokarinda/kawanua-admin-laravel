<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the default Administrator User if it doesn't exist
        $user = User::firstOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
        ]);

        // Assign Role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $user->assignRole($superAdminRole);
        }
    }
}
