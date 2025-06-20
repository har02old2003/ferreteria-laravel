<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos clientes de prueba
        $clients = [
            [
                'dni' => '12345678',
                'first_name' => 'Juan Carlos',
                'last_name' => 'Pérez García',
                'full_name' => 'Juan Carlos Pérez García',
                'phone' => '987654321',
                'email' => 'juan@example.com',
                'address' => 'Av. Los Olivos 123, Lima',
                'status' => 'active'
            ],
            [
                'dni' => '87654321',
                'first_name' => 'María Elena',
                'last_name' => 'Rodríguez López',
                'full_name' => 'María Elena Rodríguez López',
                'phone' => '912345678',
                'email' => 'maria@example.com',
                'address' => 'Jr. Las Flores 456, Lima',
                'status' => 'active'
            ],
            [
                'dni' => '55667788',
                'first_name' => 'Carlos Alberto',
                'last_name' => 'Mendoza Silva',
                'full_name' => 'Carlos Alberto Mendoza Silva',
                'phone' => '998877665',
                'email' => 'carlos@example.com',
                'address' => 'Calle Los Pinos 789, Lima',
                'status' => 'active'
            ],
            [
                'dni' => '11223344',
                'first_name' => 'Ana Lucía',
                'last_name' => 'Torres Vega',
                'full_name' => 'Ana Lucía Torres Vega',
                'phone' => '955443322',
                'email' => 'ana@example.com',
                'address' => 'Av. Central 321, Lima',
                'status' => 'active'
            ],
            [
                'dni' => '99887766',
                'first_name' => 'Roberto Miguel',
                'last_name' => 'Castillo Herrera',
                'full_name' => 'Roberto Miguel Castillo Herrera',
                'phone' => '977889900',
                'email' => 'roberto@example.com',
                'address' => 'Jr. Comercio 654, Lima',
                'status' => 'active'
            ]
        ];

        foreach ($clients as $clientData) {
            Client::firstOrCreate(
                ['dni' => $clientData['dni']], // Buscar por DNI
                $clientData // Si no existe, crear con estos datos
            );
        }

        $this->command->info('Clientes de prueba creados exitosamente.');
    }
}
