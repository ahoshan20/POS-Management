<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AuthBaseModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@dev.com',
            'email_verified_at' => now(),
            'password' => 'admin@dev.com',
            'status' => AuthBaseModel::STATUS_ACTIVE,
            'role_id' => 1,
        ]);
        $admin->assignRole($admin->role->name);
        $manager = Admin::create([
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'manager@dev.com',
            'email_verified_at' => now(),
            'password' => 'manager@dev.com',
            'status' => AuthBaseModel::STATUS_ACTIVE,
            'role_id' => 2,
        ]);
        $manager->assignRole($manager->role->name);
        $cashiar = Admin::create([
            'name' => 'Cashiar',
            'username' => 'cashiar',
            'email' => 'cashiar@dev.com',
            'password' => 'cashiar@dev.com',
            'role_id' => 2,
        ]);
        $cashiar->assignRole($cashiar->role->name);
    }
}
