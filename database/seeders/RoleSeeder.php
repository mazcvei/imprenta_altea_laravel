<?php

namespace Database\Seeders;

use App\Models\Role;
use App\RolEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $roles = [RolEnum::CLIENTE->name, RolEnum::ADMINISTRADOR->name];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
            ]);
        }
    }
}
