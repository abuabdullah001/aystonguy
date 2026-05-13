<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage settings',
            'manage roles',
            'manage users',
        ];

        foreach ($permissions as $permissionName) {
            Permission::query()->firstOrCreate(['name' => $permissionName]);
        }

        $superAdminRole = Role::query()->firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $userRole = Role::query()->firstOrCreate(['name' => 'user']);

        $superAdminRole->syncPermissions(Permission::query()->pluck('name')->all());
        $adminRole->syncPermissions(['manage settings']);
        $userRole->syncPermissions([]);

        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $user = User::query()->firstOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'User', 'password' => Hash::make('password')]
        );

        $superAdmin->syncRoles([$superAdminRole->name]);
        $admin->syncRoles([$adminRole->name]);
        $user->syncRoles([$userRole->name]);
    }
}
