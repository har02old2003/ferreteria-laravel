<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MTBFService;
use App\Services\AgileVelocityService;

class GenerateMetricsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:generate 
                            {--coverage : Generar reporte de cobertura}
                            {--mtbf : Generar reporte MTBF}
                            {--velocity : Generar reporte de velocidad ágil}
                            {--all : Generar todos los reportes}
                            {--days=30 : Número de días para análisis MTBF}
                            {--sprints=5 : Número de sprints para análisis de velocidad}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar reportes de métricas del sistema: Cobertura, MTBF y Velocidad Ágil';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 GENERADOR DE MÉTRICAS DEL SISTEMA');
        $this->info('=====================================');
        $this->newLine();

        $all = $this->option('all');
        $coverage = $this->option('coverage') || $all;
        $mtbf = $this->option('mtbf') || $all;
        $velocity = $this->option('velocity') || $all;

        if (!$coverage && !$mtbf && !$velocity) {
            $this->error('❌ Debes especificar al menos una métrica a generar.');
            $this->info('Uso: php artisan metrics:generate --all');
            return 1;
        }

        // Generar reporte de cobertura
        if ($coverage) {
            $this->generateCoverageReport();
        }

        // Generar reporte MTBF
        if ($mtbf) {
            $this->generateMTBFReport();
        }

        // Generar reporte de velocidad ágil
        if ($velocity) {
            $this->generateVelocityReport();
        }

        $this->newLine();
        $this->info('✅ Reportes generados exitosamente!');
        
        return 0;
    }

    /**
     * Generar reporte de cobertura de código
     */
    private function generateCoverageReport(): void
    {
        $this->info('📊 GENERANDO REPORTE DE COBERTURA...');
        
        try {
            // Ejecutar script de cobertura
            $output = shell_exec('php scripts/coverage-report.php 2>&1');
            
            if ($output) {
                $this->line($output);
            } else {
                $this->comment('⚠️  Ejecuta primero: composer test-coverage-clover');
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error generando reporte de cobertura: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Generar reporte MTBF
     */
    private function generateMTBFReport(): void
    {
        $this->info('🔧 GENERANDO REPORTE MTBF...');
        
        try {
            $mtbfService = new MTBFService();
            $days = (int) $this->option('days');
            $report = $mtbfService->generateReport($days);
            
            $this->displayMTBFReport($report);
            
            // Guardar reporte
            file_put_contents('mtbf-report.json', json_encode($report, JSON_PRETTY_PRINT));
            $this->comment('💾 Reporte guardado en: mtbf-report.json');
            
        } catch (\Exception $e) {
            $this->error("❌ Error generando reporte MTBF: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Generar reporte de velocidad ágil
     */
    private function generateVelocityReport(): void
    {
        $this->info('⚡ GENERANDO REPORTE DE VELOCIDAD ÁGIL...');
        
        try {
            $velocityService = new AgileVelocityService();
            $report = $velocityService->generateVelocityReport();
            
            $this->displayVelocityReport($report);
            
            // Guardar reporte
            file_put_contents('velocity-report.json', json_encode($report, JSON_PRETTY_PRINT));
            $this->comment('💾 Reporte guardado en: velocity-report.json');
            
        } catch (\Exception $e) {
            $this->error("❌ Error generando reporte de velocidad: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Mostrar reporte MTBF
     */
    private function displayMTBFReport(array $report): void
    {
        $mtbf = $report['mtbf'];
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['MTBF (días)', $mtbf['mtbf_days']],
                ['MTBF (horas)', $mtbf['mtbf_hours']],
                ['Total de fallos', $mtbf['total_failures']],
                ['Disponibilidad', $mtbf['availability_percentage'] . '%'],
                ['Estado', $mtbf['status']],
                ['Tendencia', $report['tendencia']['tendencia']]
            ]
        );

        if (!empty($report['recomendaciones'])) {
            $this->info('💡 RECOMENDACIONES:');
            foreach ($report['recomendaciones'] as $recomendacion) {
                $this->line("• {$recomendacion}");
            }
        }
    }

    /**
     * Mostrar reporte de velocidad ágil
     */
    private function displayVelocityReport(array $report): void
    {
        $velocity = $report['velocity'];
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Velocidad promedio', $velocity['average_velocity'] . ' points'],
                ['Sprints analizados', $velocity['sprints_analyzed']],
                ['Velocidad mínima', $velocity['min_velocity'] ?? 'N/A'],
                ['Velocidad máxima', $velocity['max_velocity'] ?? 'N/A'],
                ['Tendencia', $velocity['velocity_trend']],
                ['Consistencia', $velocity['velocity_consistency']['level'] ?? 'N/A'],
                ['Predicción próximo sprint', $report['predictions']['next_sprint_prediction'] ?? 'N/A'],
                ['Confianza', $report['predictions']['confidence'] ?? 'N/A']
            ]
        );

        if (!empty($velocity['recommendations'])) {
            $this->info('💡 RECOMENDACIONES:');
            foreach ($velocity['recommendations'] as $recomendacion) {
                $this->line("• {$recomendacion}");
            }
        }
    }
}
