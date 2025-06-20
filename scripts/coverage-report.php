<?php

/**
 * Script para generar reportes automáticos de cobertura de código
 * Uso: php scripts/coverage-report.php
 */

class CoverageReporter
{
    private string $coverageFile = 'coverage.xml';
    private string $reportFile = 'coverage-report.json';
    
    public function generateReport(): array
    {
        if (!file_exists($this->coverageFile)) {
            throw new Exception("No se encontró el archivo de cobertura. Ejecuta: composer test-coverage-clover");
        }
        
        $xml = simplexml_load_file($this->coverageFile);
        $metrics = $xml->xpath('//metrics')[0];
        
        $report = [
            'fecha' => date('Y-m-d H:i:s'),
            'cobertura' => [
                'lineas_totales' => (int) $metrics['statements'],
                'lineas_cubiertas' => (int) $metrics['coveredstatements'],
                'porcentaje_lineas' => $this->calculatePercentage(
                    (int) $metrics['coveredstatements'], 
                    (int) $metrics['statements']
                ),
                'metodos_totales' => (int) $metrics['methods'],
                'metodos_cubiertos' => (int) $metrics['coveredmethods'],
                'porcentaje_metodos' => $this->calculatePercentage(
                    (int) $metrics['coveredmethods'], 
                    (int) $metrics['methods']
                ),
                'clases_totales' => (int) $metrics['classes'],
                'clases_cubiertas' => (int) $metrics['coveredclasses'],
                'porcentaje_clases' => $this->calculatePercentage(
                    (int) $metrics['coveredclasses'], 
                    (int) $metrics['classes']
                )
            ],
            'calificacion' => $this->getGrade($this->calculatePercentage(
                (int) $metrics['coveredstatements'], 
                (int) $metrics['statements']
            )),
            'objetivo' => 80.0,
            'estado' => $this->calculatePercentage(
                (int) $metrics['coveredstatements'], 
                (int) $metrics['statements']
            ) >= 80 ? 'CUMPLE' : 'NO_CUMPLE'
        ];
        
        // Guardar reporte
        file_put_contents($this->reportFile, json_encode($report, JSON_PRETTY_PRINT));
        
        return $report;
    }
    
    private function calculatePercentage(int $covered, int $total): float
    {
        return $total > 0 ? round(($covered / $total) * 100, 2) : 0.0;
    }
    
    private function getGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }
    
    public function displayReport(array $report): void
    {
        echo "\n📊 REPORTE DE COBERTURA DE CÓDIGO\n";
        echo "================================\n";
        echo "📅 Fecha: {$report['fecha']}\n\n";
        
        echo "📈 MÉTRICAS DE COBERTURA:\n";
        echo "• Líneas: {$report['cobertura']['porcentaje_lineas']}% ({$report['cobertura']['lineas_cubiertas']}/{$report['cobertura']['lineas_totales']})\n";
        echo "• Métodos: {$report['cobertura']['porcentaje_metodos']}% ({$report['cobertura']['metodos_cubiertos']}/{$report['cobertura']['metodos_totales']})\n";
        echo "• Clases: {$report['cobertura']['porcentaje_clases']}% ({$report['cobertura']['clases_cubiertas']}/{$report['cobertura']['clases_totales']})\n\n";
        
        echo "🎯 CALIFICACIÓN: {$report['calificacion']}\n";
        echo "🎯 OBJETIVO: {$report['objetivo']}%\n";
        echo "🎯 ESTADO: {$report['estado']}\n\n";
        
        if ($report['estado'] === 'NO_CUMPLE') {
            echo "⚠️  RECOMENDACIONES:\n";
            echo "• Agregar más pruebas unitarias\n";
            echo "• Cubrir casos edge\n";
            echo "• Revisar código crítico sin pruebas\n";
        } else {
            echo "✅ ¡Excelente cobertura de código!\n";
        }
    }
}

// Ejecutar reporte
try {
    $reporter = new CoverageReporter();
    $report = $reporter->generateReport();
    $reporter->displayReport($report);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} 