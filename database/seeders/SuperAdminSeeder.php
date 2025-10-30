<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role Super Admin (jika belum ada)
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Beri semua permission ke Super Admin
        $permissions = Permission::pluck('name')->toArray();
        $role->syncPermissions($permissions);

        // Buat user super admin (kalau belum ada)
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Beri role Super Admin
        $user->assignRole($role);
    }
}
