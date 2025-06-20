<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MTBFService
{
    private const FAILURE_TYPES = [
        'system_error' => 'Error del Sistema',
        'database_error' => 'Error de Base de Datos',
        'validation_error' => 'Error de Validación',
        'network_error' => 'Error de Red',
        'authentication_error' => 'Error de Autenticación'
    ];

    /**
     * Registrar un fallo del sistema
     */
    public function recordFailure(string $type, string $description, array $context = []): void
    {
        DB::table('system_failures')->insert([
            'type' => $type,
            'description' => $description,
            'context' => json_encode($context),
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::error("MTBF: Fallo registrado", [
            'type' => $type,
            'description' => $description,
            'context' => $context
        ]);
    }

    /**
     * Calcular MTBF para un período específico
     */
    public function calculateMTBF(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Obtener fallos en el período
        $failures = DB::table('system_failures')
            ->where('occurred_at', '>=', $startDate)
            ->where('occurred_at', '<=', $endDate)
            ->orderBy('occurred_at')
            ->get();

        $totalFailures = $failures->count();
        
        if ($totalFailures <= 1) {
            return [
                'mtbf_hours' => 0,
                'mtbf_days' => 0,
                'total_failures' => $totalFailures,
                'period_days' => $days,
                'availability_percentage' => $totalFailures === 0 ? 100 : 95,
                'status' => $totalFailures === 0 ? 'EXCELENTE' : 'INSUFICIENTE_DATOS'
            ];
        }

        // Calcular tiempo entre fallos
        $timeBetweenFailures = [];
        for ($i = 1; $i < $totalFailures; $i++) {
            $previousFailure = Carbon::parse($failures[$i - 1]->occurred_at);
            $currentFailure = Carbon::parse($failures[$i]->occurred_at);
            $timeBetweenFailures[] = $currentFailure->diffInHours($previousFailure);
        }

        $mtbfHours = array_sum($timeBetweenFailures) / count($timeBetweenFailures);
        $mtbfDays = $mtbfHours / 24;

        return [
            'mtbf_hours' => round($mtbfHours, 2),
            'mtbf_days' => round($mtbfDays, 2),
            'total_failures' => $totalFailures,
            'period_days' => $days,
            'availability_percentage' => $this->calculateAvailability($mtbfHours),
            'status' => $this->getMTBFStatus($mtbfDays),
            'failure_breakdown' => $this->getFailureBreakdown($failures)
        ];
    }

    /**
     * Calcular disponibilidad basada en MTBF
     */
    private function calculateAvailability(float $mtbfHours): float
    {
        // Asumiendo 1 hora de downtime promedio por fallo
        $mttrHours = 1;
        $availability = ($mtbfHours / ($mtbfHours + $mttrHours)) * 100;
        return round(min($availability, 99.99), 2);
    }

    /**
     * Obtener estado del MTBF
     */
    private function getMTBFStatus(float $mtbfDays): string
    {
        if ($mtbfDays >= 30) return 'EXCELENTE';
        if ($mtbfDays >= 14) return 'BUENO';
        if ($mtbfDays >= 7) return 'REGULAR';
        return 'CRÍTICO';
    }

    /**
     * Obtener desglose de fallos por tipo
     */
    private function getFailureBreakdown($failures): array
    {
        $breakdown = [];
        foreach (self::FAILURE_TYPES as $type => $description) {
            $count = $failures->where('type', $type)->count();
            $breakdown[$type] = [
                'count' => $count,
                'description' => $description,
                'percentage' => $failures->count() > 0 ? round(($count / $failures->count()) * 100, 1) : 0
            ];
        }
        return $breakdown;
    }

    /**
     * Generar reporte MTBF
     */
    public function generateReport(int $days = 30): array
    {
        $mtbfData = $this->calculateMTBF($days);
        
        return [
            'fecha_reporte' => now()->format('Y-m-d H:i:s'),
            'periodo_analisis' => "{$days} días",
            'mtbf' => $mtbfData,
            'recomendaciones' => $this->getRecommendations($mtbfData['status']),
            'tendencia' => $this->getTrend()
        ];
    }

    /**
     * Obtener recomendaciones basadas en el estado
     */
    private function getRecommendations(string $status): array
    {
        return match($status) {
            'EXCELENTE' => [
                'Mantener las prácticas actuales',
                'Continuar monitoreo preventivo',
                'Revisar y documentar mejores prácticas'
            ],
            'BUENO' => [
                'Implementar monitoreo más frecuente',
                'Revisar logs de manera regular',
                'Considerar mejoras en infraestructura'
            ],
            'REGULAR' => [
                'Investigar causas raíz de fallos frecuentes',
                'Implementar alertas proactivas',
                'Mejorar procedimientos de recuperación'
            ],
            'CRÍTICO' => [
                'Análisis urgente de causas de fallos',
                'Implementar redundancia en sistemas críticos',
                'Revisión completa de arquitectura'
            ],
            default => ['Continuar monitoreo']
        };
    }

    /**
     * Obtener tendencia de fallos (últimos 7 vs 30 días)
     */
    private function getTrend(): array
    {
        $mtbf7Days = $this->calculateMTBF(7);
        $mtbf30Days = $this->calculateMTBF(30);

        $trend = 'ESTABLE';
        if ($mtbf7Days['mtbf_days'] > $mtbf30Days['mtbf_days'] * 1.2) {
            $trend = 'MEJORANDO';
        } elseif ($mtbf7Days['mtbf_days'] < $mtbf30Days['mtbf_days'] * 0.8) {
            $trend = 'EMPEORANDO';
        }

        return [
            'mtbf_7_dias' => $mtbf7Days['mtbf_days'],
            'mtbf_30_dias' => $mtbf30Days['mtbf_days'],
            'tendencia' => $trend
        ];
    }
} 