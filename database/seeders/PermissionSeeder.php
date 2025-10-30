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
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- BUAT PERMISSIONS (IZIN) ---
        $permissions = [
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',
            'manage team members',
            'manage users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // --- BUAT ROLES (PERAN) ---

        // 1. Role "Editor"
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $editorRole->givePermissionTo([
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',
            'manage team members',
        ]);

        // 2. Role "Super Admin"
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
