<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario empleado
        $employee = User::create([
            'name' => 'Empleado Test',
            'email' => 'empleado@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de empleado
        $employeeRole = Role::where('slug', 'employee')->first();
        if ($employeeRole) {
            $employee->roles()->attach($employeeRole->id);
        }

        echo "Usuario empleado creado: {$employee->email}\n";
    }
}
