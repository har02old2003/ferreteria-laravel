<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        
        if ($adminRole) {
            $user = User::where('email', 'admin@example.com')->first();
            if ($user) {
                $user->roles()->syncWithoutDetaching([$adminRole->id]);
            }
        }
    }
} 