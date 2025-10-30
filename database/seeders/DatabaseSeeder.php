<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil Seeder untuk membuat Akun Super Admin,
        // lalu panggil Seeder untuk membuat Roles & Permissions
        // INI ADALAH URUTAN YANG BENAR:
        $this->call([
            SuperAdminSeeder::class,
            PermissionSeeder::class,
        ]);

        // Buat 10 user bohongan untuk tes (opsional)
        // User::factory(10)->create();
    }
}