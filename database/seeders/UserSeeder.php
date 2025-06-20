<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@ferrechincha.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin'
            ]
        );

        // Employee user
        User::updateOrCreate(
            ['email' => 'employee@ferrechincha.com'],
            [
                'name' => 'Empleado',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'employee'
            ]
        );
    }
} 