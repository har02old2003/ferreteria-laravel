<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Generar reporte de ventas en CSV
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $sales = Sale::with(['user', 'client', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = $this->generateSalesCSV($sales);
        
        $filename = 'reporte_ventas_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generar reporte de productos con bajo stock
     */
    public function exportLowStock()
    {
        $products = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->with(['category', 'brand', 'supplier'])
            ->orderBy('stock', 'asc')
            ->get();

        $csvData = $this->generateLowStockCSV($products);
        
        $filename = 'productos_bajo_stock_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generar reporte completo del dashboard
     */
    public function exportDashboardReport()
    {
        $data = [
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'total_ventas' => Sale::where('status', 'completed')->sum('total'),
            'numero_ventas' => Sale::where('status', 'completed')->count(),
            'total_productos' => Product::where('is_active', true)->count(),
            'productos_bajo_stock' => Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'minimum_stock')->count(),
            'total_clientes' => Client::where('status', 'active')->count(),
            'ventas_mes_actual' => Sale::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)->sum('total'),
        ];

        $csvData = $this->generateDashboardCSV($data);
        
        $filename = 'reporte_dashboard_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generar datos CSV para ventas
     */
    private function generateSalesCSV($sales)
    {
        $output = fopen('php://temp', 'r+');
        
        // Encabezados
        fputcsv($output, [
            'Código de Venta',
            'Fecha',
            'Cliente',
            'Vendedor',
            'Total',
            'Estado',
            'Productos Vendidos',
            'Cantidad Total Items'
        ]);

        // Datos
        foreach ($sales as $sale) {
            $products = $sale->items->pluck('product.name')->join(', ');
            $totalItems = $sale->items->sum('quantity');
            
            fputcsv($output, [
                $sale->code ?? 'V' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                $sale->created_at->format('d/m/Y H:i'),
                $sale->client ? $sale->client->full_name : 'Cliente General',
                $sale->user->name,
                'S/ ' . number_format($sale->total, 2),
                ucfirst($sale->status),
                $products,
                $totalItems
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generar datos CSV para productos con bajo stock
     */
    private function generateLowStockCSV($products)
    {
        $output = fopen('php://temp', 'r+');
        
        // Encabezados
        fputcsv($output, [
            'Código',
            'Nombre del Producto',
            'Categoría',
            'Marca',
            'Proveedor',
            'Stock Actual',
            'Stock Mínimo',
            'Precio de Venta',
            'Estado de Alerta'
        ]);

        // Datos
        foreach ($products as $product) {
            $alertLevel = $product->stock == 0 ? 'SIN STOCK' : 
                         ($product->stock <= ($product->minimum_stock * 0.5) ? 'CRÍTICO' : 'BAJO');
            
            fputcsv($output, [
                $product->code,
                $product->name,
                $product->category->name ?? 'Sin categoría',
                $product->brand->name ?? 'Sin marca',
                $product->supplier->name ?? 'Sin proveedor',
                $product->stock,
                $product->minimum_stock,
                'S/ ' . number_format($product->sale_price, 2),
                $alertLevel
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generar datos CSV para reporte del dashboard
     */
    private function generateDashboardCSV($data)
    {
        $output = fopen('php://temp', 'r+');
        
        // Encabezados
        fputcsv($output, ['Métrica', 'Valor']);

        // Datos
        fputcsv($output, ['Fecha de Generación', $data['fecha_generacion']]);
        fputcsv($output, ['Total en Ventas', 'S/ ' . number_format($data['total_ventas'], 2)]);
        fputcsv($output, ['Número de Ventas', $data['numero_ventas']]);
        fputcsv($output, ['Total de Productos', $data['total_productos']]);
        fputcsv($output, ['Productos con Bajo Stock', $data['productos_bajo_stock']]);
        fputcsv($output, ['Total de Clientes', $data['total_clientes']]);
        fputcsv($output, ['Ventas del Mes Actual', 'S/ ' . number_format($data['ventas_mes_actual'], 2)]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Vista principal de reportes
     */
    public function index()
    {
        return inertia('Admin/Reports/Index', [
            'stats' => [
                'total_sales' => Sale::where('status', 'completed')->count(),
                'total_products' => Product::where('is_active', true)->count(),
                'low_stock_products' => Product::where('is_active', true)
                    ->whereColumn('stock', '<=', 'minimum_stock')->count(),
                'total_clients' => Client::where('status', 'active')->count(),
            ]
        ]);
    }
} 