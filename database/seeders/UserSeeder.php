<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario admin
        \App\Models\User::create([
            'name' => 'Admin',
            'lastname1' => 'User',
            'lastname2' => 'User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role_id' => \App\RolEnum::ADMINISTRADOR->value,
        ]);

        // Crear un usuario cliente
        \App\Models\User::create([
            'name' => 'Client',
            'lastname1' => 'User',
            'lastname2' => 'User',
            'email' => 'client@client.com',
            'password' => bcrypt('password'),
            'role_id' => \App\RolEnum::CLIENTE->value,
        ]);
    }
}
