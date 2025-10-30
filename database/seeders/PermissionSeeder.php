<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- BUAT PERMISSIONS (PASTIKAN TIDAK DUPLIKAT) ---
        $permissions = [
            // Donasi
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',

            // Team members
            'manage team members',

            // User management
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- BUAT ROLE (PASTIKAN TIDAK DUPLIKAT) ---
        // 1. Role "Editor"
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $editorRole->syncPermissions([
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',
            'manage team members',
        ]);

        // 2. Role "Super Admin"
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());
    }
}
