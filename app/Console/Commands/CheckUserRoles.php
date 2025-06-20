<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserRoles extends Command
{
    protected $signature = 'users:check-roles';
    protected $description = 'Check roles assigned to users';

    public function handle()
    {
        $users = User::with('roles')->get();

        foreach ($users as $user) {
            $this->info("User: {$user->name} ({$user->email})");
            $this->info("Roles: " . $user->roles->pluck('slug')->implode(', '));
            $this->info("-------------------");
        }
    }
} 