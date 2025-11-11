<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@digitup.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Agent Immobilier',
            'email' => 'agent@digitup.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Agent Alger',
            'email' => 'agent.alger@digitup.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Visiteur',
            'email' => 'guest@digitup.com',
            'password' => Hash::make('password'),
            'role' => 'guest'
        ]);
    }
}
