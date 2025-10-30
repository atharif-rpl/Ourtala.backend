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
        // Izin untuk Donasi
        Permission::create(['name' => 'view donations']);
        Permission::create(['name' => 'create donations']);
        Permission::create(['name' => 'edit donations']);
        Permission::create(['name' => 'delete donations']);

        // Izin untuk Team Members
        Permission::create(['name' => 'manage team members']);

        // Izin untuk Users (Hanya Super Admin)
        Permission::create(['name' => 'manage users']);

        // --- BUAT ROLES (PERAN) ---

        // 1. Role "Editor"
        // (Editor bisa mengurus donasi, tapi tidak bisa mengurus user)
        $editorRole = Role::create(['name' => 'Editor']);
        $editorRole->givePermissionTo([
            'view donations',
            'create donations',
            'edit donations',
            'delete donations',
            'manage team members',
        ]);

        // 2. Role "Super Admin"
        // (Ambil role Super Admin yang sudah ada)
        $superAdminRole = Role::findByName('Super Admin');
        // Beri semua izin yang baru dibuat (termasuk 'manage users')
        $superAdminRole->givePermissionTo(Permission::all());
    }
}