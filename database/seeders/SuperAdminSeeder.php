<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2️⃣ Buat daftar permission dasar (kalau belum ada)
        $permissions = [
            'manage users',
            'manage roles',
            'manage donations',
            'manage campaigns',
            'manage members',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3️⃣ Buat role Super Admin
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // 4️⃣ Beri semua permission ke role Super Admin
        $role->givePermissionTo(Permission::all());

        // 5️⃣ Buat akun Super Admin utama
        $user = User::updateOrCreate(
            ['email' => 'admin@ourtala.id'],
            [
                'name' => 'Atharif (Master Admin)',
                'password' => Hash::make('ourtala123'),
            ]
        );

        // 6️⃣ Assign role Super Admin ke user
        $user->assignRole($role);
    }
}
