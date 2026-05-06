<?php

namespace Database\Seeders;

use App\Constants\RoleAndPermissions;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::query()->updateOrCreate([
            'email' => 'dsd@eppms.ng',
        ], [
            'name' => 'System Admin',
            'password' => Hash::make('Admin@1234'),
        ]);

        $admin->assignRole(RoleAndPermissions::ADMIN);
    }
}
