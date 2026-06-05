<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'kolektor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin_desa', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@protika.id'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('Admin@123'),
            ]
        );
        $admin->assignRole('superadmin');
    }
}
