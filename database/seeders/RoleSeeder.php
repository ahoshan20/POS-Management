<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            1 => 'Admin',
            2 => 'Manager',
            3 => 'Cashier',
        ];

        foreach ($roles as $roleId => $roleName) {
            Role::create(['id' => $roleId, 'name' => $roleName, 'guard_name' => 'admin']);
        }
    }
}
