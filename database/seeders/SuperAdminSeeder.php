<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar; // <-- 1. TAMBAHKAN IMPORT INI

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 2. RESET CACHE DULU
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat role "Super Admin"
        $role = Role::create(['name' => 'Super Admin']);

        // Buat permission wildcard
        Permission::create(['name' => '*']);

        // Beri role ini semua izin
        $role->givePermissionTo('*');

        // Buat akun master Anda
        $user = User::create([
            'name' => 'Atharif (Master Admin)',
            'email' => 'admin@ourtala.id', // Ganti dengan email Anda
            'password' => bcrypt('Ourtala123') // Ganti password ini
        ]);

        // Tetapkan role "Super Admin" ke user Anda
        $user->assignRole($role);
    }
}