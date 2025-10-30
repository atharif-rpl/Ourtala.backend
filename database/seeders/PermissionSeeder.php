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
        // Reset cache dulu
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- BUAT PERMISSIONS (IZIN) ---
        Permission::create(['name' => 'view donations']);
        Permission::create(['name' => 'create donations']);
        Permission::create(['name' => 'edit donations']);
        Permission::create(['name' => 'delete donations']);
        Permission::create(['name' => 'manage team members']);
        Permission::create(['name' => 'manage users']);

        // --- FIX: RESET CACHE LAGI SETELAH MEMBUAT PERMISSIONS ---
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- BUAT ROLES (PERAN) ---

        // 1. Role "Editor"
        $editorRole = Role::create(['name' => 'Editor']);
        $editorRole->givePermissionTo([
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',
            'manage team members',
        ]);

        // 2. Role "Super Admin"
        $superAdminRole = Role::findByName('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());
    }
}