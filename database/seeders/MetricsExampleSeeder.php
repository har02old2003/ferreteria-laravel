<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetricsExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedSystemFailures();
        $this->seedAgileSprints();
        $this->seedAgileStories();
    }

    /**
     * Crear datos de ejemplo para fallos del sistema
     */
    private function seedSystemFailures(): void
    {
        $failures = [
            [
                'type' => 'database_error',
                'description' => 'Connection timeout en base de datos',
                'context' => json_encode(['query' => 'SELECT * FROM products', 'duration' => '30s']),
                'occurred_at' => Carbon::now()->subDays(25),
                'severity' => 'high',
                'component' => 'ProductController',
                'resolved' => true,
                'resolved_at' => Carbon::now()->subDays(25)->addHours(2),
                'resolution_notes' => 'Optimizado índices de base de datos'
            ],
            [
                'type' => 'validation_error',
                'description' => 'Error en validación de precio de producto',
                'context' => json_encode(['field' => 'price', 'value' => '-100']),
                'occurred_at' => Carbon::now()->subDays(15),
                'severity' => 'medium',
                'component' => 'ProductForm',
                'resolved' => true,
                'resolved_at' => Carbon::now()->subDays(15)->addHours(1),
                'resolution_notes' => 'Agregada validación de precio mínimo'
            ],
            [
                'type' => 'authentication_error',
                'description' => 'Fallo en autenticación de usuario',
                'context' => json_encode(['user_id' => 123, 'ip' => '192.168.1.100']),
                'occurred_at' => Carbon::now()->subDays(8),
                'severity' => 'medium',
                'component' => 'AuthController',
                'resolved' => true,
                'resolved_at' => Carbon::now()->subDays(8)->addMinutes(30),
                'resolution_notes' => 'Sesión caducada, usuario reautenticado'
            ],
            [
                'type' => 'system_error',
                'description' => 'Error de memoria insuficiente',
                'context' => json_encode(['memory_usage' => '512MB', 'limit' => '256MB']),
                'occurred_at' => Carbon::now()->subDays(3),
                'severity' => 'critical',
                'component' => 'ReportGenerator',
                'resolved' => true,
                'resolved_at' => Carbon::now()->subDays(3)->addHours(4),
                'resolution_notes' => 'Aumentado límite de memoria a 512MB'
            ]
        ];

        foreach ($failures as $failure) {
            DB::table('system_failures')->insert([
                ...$failure,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Crear datos de ejemplo para sprints ágiles
     */
    private function seedAgileSprints(): void
    {
        $sprints = [
            [
                'sprint_name' => 'Sprint-1-2025',
                'start_date' => Carbon::now()->subDays(56),
                'end_date' => Carbon::now()->subDays(42),
                'status' => 'completed',
                'planned_points' => 40,
                'completed_points' => 35,
                'completion_rate' => 87.5,
                'sprint_goal' => 'Implementar módulo de productos básico',
                'retrospective_notes' => 'Buena velocidad, mejorar estimaciones'
            ],
            [
                'sprint_name' => 'Sprint-2-2025',
                'start_date' => Carbon::now()->subDays(42),
                'end_date' => Carbon::now()->subDays(28),
                'status' => 'completed',
                'planned_points' => 45,
                'completed_points' => 42,
                'completion_rate' => 93.3,
                'sprint_goal' => 'Sistema de ventas y reportes',
                'retrospective_notes' => 'Excelente trabajo en equipo'
            ],
            [
                'sprint_name' => 'Sprint-3-2025',
                'start_date' => Carbon::now()->subDays(28),
                'end_date' => Carbon::now()->subDays(14),
                'status' => 'completed',
                'planned_points' => 38,
                'completed_points' => 38,
                'completion_rate' => 100.0,
                'sprint_goal' => 'Autenticación y roles de usuario',
                'retrospective_notes' => 'Sprint perfecto, todas las historias completadas'
            ],
            [
                'sprint_name' => 'Sprint-4-2025',
                'start_date' => Carbon::now()->subDays(14),
                'end_date' => Carbon::now(),
                'status' => 'active',
                'planned_points' => 42,
                'completed_points' => 25,
                'completion_rate' => 59.5,
                'sprint_goal' => 'Métricas y monitoreo del sistema',
                'retrospective_notes' => null
            ]
        ];

        foreach ($sprints as $sprint) {
            DB::table('agile_sprints')->insert([
                ...$sprint,
                'planned_stories' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Crear datos de ejemplo para historias de usuario
     */
    private function seedAgileStories(): void
    {
        $stories = [
            // Sprint 1
            [
                'story_id' => 'FERRE-001',
                'title' => 'Como administrador quiero crear productos',
                'description' => 'Implementar CRUD básico de productos',
                'story_points' => 8,
                'sprint_name' => 'Sprint-1-2025',
                'assigned_to' => 'desarrollador1@empresa.com',
                'status' => 'completed',
                'priority' => 'high',
                'epic' => 'Gestión de Productos',
                'started_at' => Carbon::now()->subDays(50),
                'completed_at' => Carbon::now()->subDays(48)
            ],
            [
                'story_id' => 'FERRE-002',
                'title' => 'Como usuario quiero buscar productos',
                'description' => 'Implementar búsqueda y filtros',
                'story_points' => 13,
                'sprint_name' => 'Sprint-1-2025',
                'assigned_to' => 'desarrollador2@empresa.com',
                'status' => 'completed',
                'priority' => 'medium',
                'epic' => 'Gestión de Productos',
                'started_at' => Carbon::now()->subDays(49),
                'completed_at' => Carbon::now()->subDays(45)
            ],
            [
                'story_id' => 'FERRE-003',
                'title' => 'Como administrador quiero gestionar categorías',
                'description' => 'CRUD de categorías de productos',
                'story_points' => 5,
                'sprint_name' => 'Sprint-1-2025',
                'assigned_to' => 'desarrollador1@empresa.com',
                'status' => 'completed',
                'priority' => 'medium',
                'epic' => 'Gestión de Productos',
                'started_at' => Carbon::now()->subDays(47),
                'completed_at' => Carbon::now()->subDays(44)
            ],
            [
                'story_id' => 'FERRE-004',
                'title' => 'Como administrador quiero gestionar marcas',
                'description' => 'CRUD de marcas de productos',
                'story_points' => 9,
                'sprint_name' => 'Sprint-1-2025',
                'assigned_to' => 'desarrollador3@empresa.com',
                'status' => 'completed',
                'priority' => 'low',
                'epic' => 'Gestión de Productos',
                'started_at' => Carbon::now()->subDays(46),
                'completed_at' => Carbon::now()->subDays(43)
            ],
            // Sprint 2
            [
                'story_id' => 'FERRE-005',
                'title' => 'Como empleado quiero registrar ventas',
                'description' => 'Implementar proceso de venta',
                'story_points' => 21,
                'sprint_name' => 'Sprint-2-2025',
                'assigned_to' => 'desarrollador1@empresa.com',
                'status' => 'completed',
                'priority' => 'critical',
                'epic' => 'Sistema de Ventas',
                'started_at' => Carbon::now()->subDays(40),
                'completed_at' => Carbon::now()->subDays(35)
            ],
            [
                'story_id' => 'FERRE-006',
                'title' => 'Como administrador quiero ver reportes de ventas',
                'description' => 'Dashboard con métricas de ventas',
                'story_points' => 13,
                'sprint_name' => 'Sprint-2-2025',
                'assigned_to' => 'desarrollador2@empresa.com',
                'status' => 'completed',
                'priority' => 'high',
                'epic' => 'Sistema de Ventas',
                'started_at' => Carbon::now()->subDays(38),
                'completed_at' => Carbon::now()->subDays(32)
            ],
            [
                'story_id' => 'FERRE-007',
                'title' => 'Como empleado quiero gestionar clientes',
                'description' => 'CRUD de clientes con búsqueda por DNI',
                'story_points' => 8,
                'sprint_name' => 'Sprint-2-2025',
                'assigned_to' => 'desarrollador3@empresa.com',
                'status' => 'completed',
                'priority' => 'medium',
                'epic' => 'Sistema de Ventas',
                'started_at' => Carbon::now()->subDays(36),
                'completed_at' => Carbon::now()->subDays(30)
            ]
        ];

        foreach ($stories as $story) {
            DB::table('agile_stories')->insert([
                ...$story,
                'acceptance_criteria' => json_encode([
                    'Criterio 1: Funcionalidad básica implementada',
                    'Criterio 2: Pruebas unitarias escritas',
                    'Criterio 3: Documentación actualizada'
                ]),
                'metadata' => json_encode(['complexity' => 'medium', 'effort' => 'standard']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
} 