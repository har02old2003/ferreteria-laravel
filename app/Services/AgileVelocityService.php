<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgileVelocityService
{
    private const SPRINT_DURATION_WEEKS = 2; // Duración de sprint en semanas
    
    /**
     * Registrar historia de usuario completada
     */
    public function recordStoryCompleted(
        string $storyId,
        string $title,
        int $storyPoints,
        string $sprintName,
        string $assignedTo,
        array $metadata = []
    ): void {
        DB::table('agile_stories')->insert([
            'story_id' => $storyId,
            'title' => $title,
            'story_points' => $storyPoints,
            'sprint_name' => $sprintName,
            'assigned_to' => $assignedTo,
            'status' => 'completed',
            'completed_at' => now(),
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Iniciar un nuevo sprint
     */
    public function startSprint(
        string $sprintName,
        Carbon $startDate,
        Carbon $endDate,
        array $plannedStories = []
    ): void {
        DB::table('agile_sprints')->insert([
            'sprint_name' => $sprintName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'planned_points' => array_sum(array_column($plannedStories, 'points')),
            'planned_stories' => json_encode($plannedStories),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Finalizar sprint
     */
    public function completeSprint(string $sprintName): array
    {
        $sprint = DB::table('agile_sprints')
            ->where('sprint_name', $sprintName)
            ->first();

        if (!$sprint) {
            throw new \Exception("Sprint '{$sprintName}' no encontrado");
        }

        // Calcular puntos completados
        $completedPoints = DB::table('agile_stories')
            ->where('sprint_name', $sprintName)
            ->where('status', 'completed')
            ->sum('story_points');

        // Actualizar sprint
        DB::table('agile_sprints')
            ->where('sprint_name', $sprintName)
            ->update([
                'status' => 'completed',
                'completed_points' => $completedPoints,
                'completion_rate' => $sprint->planned_points > 0 
                    ? round(($completedPoints / $sprint->planned_points) * 100, 1)
                    : 0,
                'updated_at' => now()
            ]);

        return $this->getSprintSummary($sprintName);
    }

    /**
     * Calcular velocidad del equipo
     */
    public function calculateVelocity(int $numberOfSprints = 5): array
    {
        $completedSprints = DB::table('agile_sprints')
            ->where('status', 'completed')
            ->orderBy('end_date', 'desc')
            ->limit($numberOfSprints)
            ->get();

        if ($completedSprints->count() === 0) {
            return [
                'average_velocity' => 0,
                'velocity_trend' => 'NO_DATA',
                'sprints_analyzed' => 0,
                'recommendations' => ['Completar al menos un sprint para calcular velocidad']
            ];
        }

        $velocities = $completedSprints->pluck('completed_points')->toArray();
        $averageVelocity = array_sum($velocities) / count($velocities);

        return [
            'average_velocity' => round($averageVelocity, 1),
            'velocity_trend' => $this->calculateTrend($velocities),
            'sprints_analyzed' => $completedSprints->count(),
            'last_sprints_velocity' => $velocities,
            'min_velocity' => min($velocities),
            'max_velocity' => max($velocities),
            'velocity_consistency' => $this->calculateConsistency($velocities),
            'recommendations' => $this->getVelocityRecommendations($velocities)
        ];
    }

    /**
     * Calcular tendencia de velocidad
     */
    private function calculateTrend(array $velocities): string
    {
        if (count($velocities) < 2) return 'INSUFICIENTE_DATOS';

        $recent = array_slice($velocities, 0, 2); // Últimos 2 sprints
        $older = array_slice($velocities, 2); // Sprints anteriores

        if (empty($older)) return 'ESTABLE';

        $recentAvg = array_sum($recent) / count($recent);
        $olderAvg = array_sum($older) / count($older);

        $changePercent = (($recentAvg - $olderAvg) / $olderAvg) * 100;

        if ($changePercent > 15) return 'MEJORANDO';
        if ($changePercent < -15) return 'DECLINANDO';
        return 'ESTABLE';
    }

    /**
     * Calcular consistencia de velocidad
     */
    private function calculateConsistency(array $velocities): array
    {
        if (count($velocities) < 2) {
            return ['score' => 0, 'level' => 'INSUFICIENTE_DATOS'];
        }

        $average = array_sum($velocities) / count($velocities);
        $variance = 0;

        foreach ($velocities as $velocity) {
            $variance += pow($velocity - $average, 2);
        }

        $standardDeviation = sqrt($variance / count($velocities));
        $coefficientOfVariation = $average > 0 ? ($standardDeviation / $average) * 100 : 100;

        $consistencyScore = max(0, 100 - $coefficientOfVariation);

        $level = match(true) {
            $consistencyScore >= 80 => 'EXCELENTE',
            $consistencyScore >= 60 => 'BUENA',
            $consistencyScore >= 40 => 'REGULAR',
            default => 'INCONSISTENTE'
        };

        return [
            'score' => round($consistencyScore, 1),
            'level' => $level,
            'standard_deviation' => round($standardDeviation, 1)
        ];
    }

    /**
     * Obtener recomendaciones basadas en velocidad
     */
    private function getVelocityRecommendations(array $velocities): array
    {
        $consistency = $this->calculateConsistency($velocities);
        $trend = $this->calculateTrend($velocities);

        $recommendations = [];

        if ($consistency['level'] === 'INCONSISTENTE') {
            $recommendations[] = 'Mejorar estimación de story points';
            $recommendations[] = 'Revisar definición de "Done"';
        }

        if ($trend === 'DECLINANDO') {
            $recommendations[] = 'Analizar causas de reducción en velocidad';
            $recommendations[] = 'Revisar carga de trabajo del equipo';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Continuar con prácticas actuales';
        }

        return $recommendations;
    }

    /**
     * Generar reporte completo de velocidad
     */
    public function generateVelocityReport(): array
    {
        $velocityData = $this->calculateVelocity();
        
        return [
            'fecha_reporte' => now()->format('Y-m-d H:i:s'),
            'velocity' => $velocityData,
            'predictions' => $this->getVelocityPredictions($velocityData)
        ];
    }

    /**
     * Obtener progreso del sprint actual
     */
    private function getCurrentSprintProgress(): array
    {
        $currentSprint = DB::table('agile_sprints')
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$currentSprint) {
            return ['status' => 'NO_ACTIVE_SPRINT'];
        }

        $completedPoints = DB::table('agile_stories')
            ->where('sprint_name', $currentSprint->sprint_name)
            ->where('status', 'completed')
            ->sum('story_points');

        $totalDays = Carbon::parse($currentSprint->start_date)
            ->diffInDays(Carbon::parse($currentSprint->end_date));
        
        $elapsedDays = Carbon::parse($currentSprint->start_date)
            ->diffInDays(now());

        return [
            'sprint_name' => $currentSprint->sprint_name,
            'planned_points' => $currentSprint->planned_points,
            'completed_points' => $completedPoints,
            'progress_percentage' => $currentSprint->planned_points > 0 
                ? round(($completedPoints / $currentSprint->planned_points) * 100, 1)
                : 0,
            'days_elapsed' => $elapsedDays,
            'total_days' => $totalDays,
            'time_progress' => round(($elapsedDays / $totalDays) * 100, 1)
        ];
    }

    /**
     * Obtener métricas del equipo
     */
    private function getTeamMetrics(): array
    {
        $last30Days = Carbon::now()->subDays(30);

        return [
            'total_stories_completed' => DB::table('agile_stories')
                ->where('completed_at', '>=', $last30Days)
                ->count(),
            'total_points_delivered' => DB::table('agile_stories')
                ->where('completed_at', '>=', $last30Days)
                ->sum('story_points'),
            'active_team_members' => DB::table('agile_stories')
                ->where('completed_at', '>=', $last30Days)
                ->distinct('assigned_to')
                ->count('assigned_to')
        ];
    }

    /**
     * Obtener predicciones de velocidad
     */
    private function getVelocityPredictions(array $velocityData): array
    {
        if ($velocityData['average_velocity'] === 0) {
            return ['next_sprint_prediction' => 0, 'confidence' => 'LOW'];
        }

        $confidence = match($velocityData['velocity_consistency']['level']) {
            'EXCELENTE' => 'HIGH',
            'BUENA' => 'MEDIUM',
            default => 'LOW'
        };

        return [
            'next_sprint_prediction' => round($velocityData['average_velocity'], 0),
            'confidence' => $confidence,
            'recommended_commitment' => round($velocityData['average_velocity'] * 0.9, 0)
        ];
    }

    /**
     * Obtener resumen de sprint
     */
    private function getSprintSummary(string $sprintName): array
    {
        $sprint = DB::table('agile_sprints')
            ->where('sprint_name', $sprintName)
            ->first();

        $stories = DB::table('agile_stories')
            ->where('sprint_name', $sprintName)
            ->get();

        return [
            'sprint_name' => $sprintName,
            'planned_points' => $sprint->planned_points,
            'completed_points' => $sprint->completed_points,
            'completion_rate' => $sprint->completion_rate,
            'stories_completed' => $stories->where('status', 'completed')->count(),
            'total_stories' => $stories->count()
        ];
    }
} 