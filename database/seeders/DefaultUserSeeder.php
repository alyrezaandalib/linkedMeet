<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'super admin',
            'email' => 'super@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('superadmin'),
        ]);

        $superAdmin->assignRole('Super Admin');
    }
}
