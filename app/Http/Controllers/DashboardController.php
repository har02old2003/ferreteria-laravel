<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the unified dashboard view.
     */
    public function index(): Response
    {
        $user = auth()->user();
        
        try {
            // Obtener estadísticas generales con valores por defecto
            $totalSales = Sale::where('status', 'completed')->sum('total') ?? 0;
            $salesCount = Sale::where('status', 'completed')->count() ?? 0;
            $productsCount = Product::where('is_active', true)->count() ?? 0;
            $newProductsCount = Product::where('is_active', true)
                ->where('created_at', '>=', Carbon::now()->subMonth())
                ->count() ?? 0;
            
            // Contar usuarios que no son admin
            $customersCount = User::where('role', '!=', 'admin')->count() ?? 0;
            
            $newCustomersCount = User::where('role', '!=', 'admin')
                ->where('created_at', '>=', Carbon::now()->subMonth())->count() ?? 0;
            
            $categoriesCount = Category::where('is_active', true)->count() ?? 0;
            $newCategoriesCount = Category::where('is_active', true)
                ->where('created_at', '>=', Carbon::now()->subMonth())
                ->count() ?? 0;

            // Calcular porcentaje de cambio en ventas
            $lastMonthSales = Sale::where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(2))
                ->where('created_at', '<', Carbon::now()->subMonth())
                ->sum('total') ?? 0;
            
            $salesPercentage = 0;
            $salesTrend = 'up';
            if ($lastMonthSales > 0) {
                $currentMonthSales = Sale::where('status', 'completed')
                    ->where('created_at', '>=', Carbon::now()->subMonth())
                    ->sum('total') ?? 0;
                $salesPercentage = round((($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1);
                $salesTrend = $salesPercentage >= 0 ? 'up' : 'down';
                $salesPercentage = abs($salesPercentage);
            }

            // Productos con bajo stock
            $lowStockProducts = Product::where('is_active', true)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id ?? 0,
                        'name' => $product->name ?? 'Producto sin nombre',
                        'stock' => $product->stock ?? 0,
                        'minimum_stock' => $product->minimum_stock ?? 0,
                    ];
                });

            // Productos próximos a vencer
            $expiringProducts = Product::where('is_active', true)
                ->where('has_expiration', true)
                ->whereNotNull('expiration_date')
                ->where('expiration_date', '<=', Carbon::now()->addDays(90))
                ->get()
                ->map(function ($product) {
                    $expirationDate = $product->expiration_date ? Carbon::parse($product->expiration_date) : Carbon::now();
                    $daysUntilExpiration = Carbon::now()->diffInDays($expirationDate, false);
                    return [
                        'id' => $product->id ?? 0,
                        'name' => $product->name ?? 'Producto sin nombre',
                        'expiration_date' => $expirationDate->format('d/m/Y'),
                        'days_until_expiration' => max(0, $daysUntilExpiration),
                    ];
                });

            // Ventas recientes
            $recentSales = Sale::with(['user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id ?? 0,
                        'code' => $sale->code ?? 'SIN-CODIGO',
                        'total' => $sale->total ?? 0,
                        'status' => $sale->status ?? 'unknown',
                        'user' => $sale->user ? $sale->user->name : 'Usuario eliminado',
                        'created_at' => $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : 'Fecha no disponible',
                    ];
                });

            // Datos de ventas por día (últimos 7 días)
            $salesByDay = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dailySales = Sale::where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('total') ?? 0;
                
                $salesByDay[] = [
                    'date' => $date->format('d/m'),
                    'total' => (float) $dailySales,
                ];
            }

            // Productos más vendidos (solo para admin) - Corregido
            $topProducts = [];
            if ($user && $user->role === 'admin') {
                try {
                    // Usar una consulta más simple y segura
                    $topProductsQuery = DB::table('sale_items')
                        ->join('products', 'sale_items.product_id', '=', 'products.id')
                        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'completed')
                        ->where('products.is_active', true)
                        ->select('products.id', 'products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
                        ->groupBy('products.id', 'products.name')
                        ->orderBy('total_sold', 'desc')
                        ->take(5)
                        ->get();

                    $totalSoldItems = $topProductsQuery->sum('total_sold');

                    $topProducts = $topProductsQuery->map(function ($product) use ($totalSoldItems) {
                        $percentage = $totalSoldItems > 0 ? ($product->total_sold / $totalSoldItems) * 100 : 0;
                        return [
                            'name' => $product->name ?? 'Producto sin nombre',
                            'total_sold' => $product->total_sold ?? 0,
                            'percentage' => round($percentage, 1),
                        ];
                    })->toArray();
                } catch (\Exception $e) {
                    Log::warning('Error calculating top products: ' . $e->getMessage());
                    $topProducts = [];
                }
            }

            $data = [
                'totals' => [
                    'sales' => [
                        'amount' => 'S/ ' . number_format($totalSales, 2),
                        'percentage' => $salesPercentage,
                        'trend' => $salesTrend,
                    ],
                    'products' => [
                        'count' => $productsCount,
                        'new' => $newProductsCount,
                    ],
                    'customers' => [
                        'count' => $customersCount,
                        'new' => $newCustomersCount,
                    ],
                    'categories' => [
                        'count' => $categoriesCount,
                        'new' => $newCategoriesCount,
                    ],
                ],
                'alerts' => [
                    'lowStock' => $lowStockProducts->toArray(),
                    'expiring' => $expiringProducts->toArray(),
                ],
                'recentSales' => $recentSales->toArray(),
                'topProducts' => $topProducts,
                'salesByDay' => $salesByDay,
                'userRole' => $user && $user->role === 'admin' ? 'admin' : 'employee',
            ];

            Log::info('Unified Dashboard data loaded successfully');

            return Inertia::render('Dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Error in DashboardController:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Dashboard', [
                'totals' => [
                    'sales' => [
                        'amount' => 'S/ 0.00',
                        'percentage' => 0,
                        'trend' => 'up',
                    ],
                    'products' => [
                        'count' => 0,
                        'new' => 0,
                    ],
                    'customers' => [
                        'count' => 0,
                        'new' => 0,
                    ],
                    'categories' => [
                        'count' => 0,
                        'new' => 0,
                    ],
                ],
                'alerts' => [
                    'lowStock' => [],
                    'expiring' => [],
                ],
                'recentSales' => [],
                'topProducts' => [],
                'salesByDay' => [],
                'userRole' => $user && $user->role === 'admin' ? 'admin' : 'employee',
                'error' => 'Hubo un error al cargar los datos del dashboard'
            ]);
        }
    }
} 