<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Role;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales generales
        $totalSales = (float)(Sale::sum('total') ?? 0);
        $totalProducts = (int)(Product::count() ?? 0);
        
        // Total de clientes desde la tabla clients
        $totalCustomers = (int)(Client::where('status', 'active')->count() ?? 0);
        
        $totalCategories = (int)(Category::count() ?? 0);

        // Incrementos
        $lastMonthSales = (float)(Sale::whereMonth('created_at', Carbon::now()->subMonth())->sum('total') ?? 0);
        $thisMonthSales = (float)(Sale::whereMonth('created_at', Carbon::now())->sum('total') ?? 0);
        $salesPercentage = $lastMonthSales > 0 ? 
            round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 2) : 0;

        $lastMonthProducts = Product::whereMonth('created_at', Carbon::now()->subMonth())->count() ?? 0;
        $thisMonthProducts = Product::whereMonth('created_at', Carbon::now())->count() ?? 0;
        $newProducts = $thisMonthProducts - $lastMonthProducts;

        // Nuevos clientes desde la tabla clients
        $lastMonthCustomers = Client::where('status', 'active')
            ->whereMonth('created_at', Carbon::now()->subMonth())
            ->count() ?? 0;
            
        $thisMonthCustomers = Client::where('status', 'active')
            ->whereMonth('created_at', Carbon::now())
            ->count() ?? 0;
            
        $newCustomers = $thisMonthCustomers - $lastMonthCustomers;

        $lastMonthCategories = Category::whereMonth('created_at', Carbon::now()->subMonth())->count() ?? 0;
        $thisMonthCategories = Category::whereMonth('created_at', Carbon::now())->count() ?? 0;
        $newCategories = $thisMonthCategories - $lastMonthCategories;

        // Ventas recientes
        $recentSales = Sale::with(['user', 'products'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'code' => $sale->code,
                    'total' => $sale->total,
                    'user' => $sale->user->name,
                    'date' => $sale->created_at->format('Y-m-d H:i:s'),
                    'status' => $sale->status,
                    'products' => $sale->products->map(function ($product) {
                        return [
                            'name' => $product->name,
                            'quantity' => $product->pivot->quantity,
                            'price' => $product->pivot->price
                        ];
                    })
                ];
            });

        // Productos más vendidos
        $topProducts = DB::table('sale_product')
            ->join('products', 'sale_product.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_product.quantity) as total_sold'),
                DB::raw('(SUM(sale_product.quantity) * 100.0 / (SELECT SUM(quantity) FROM sale_product)) as percentage')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Ventas por día de los últimos 7 días
        $salesByDay = Sale::whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total), 0) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($sale) {
                return [
                    'date' => Carbon::parse($sale->date)->format('d/m'),
                    'count' => $sale->count,
                    'total' => (float)$sale->total
                ];
            });

        // Completar días faltantes con valores en 0 para los últimos 7 días
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $existingSale = $salesByDay->firstWhere('date', $date->format('d/m'));
            $last7Days->push($existingSale ?? [
                'date' => $date->format('d/m'),
                'count' => 0,
                'total' => 0
            ]);
        }
        $salesByDay = $last7Days;

        // Alertas de productos - Stock bajo automático (≤5)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'minimum_stock' => 5, // Stock mínimo automático
                    'alert_type' => 'low_stock'
                ];
            });

        $expiringProducts = Product::where('has_expiration', true)
            ->whereNotNull('expiration_date')
            ->where('is_active', true)
            ->where('expiration_date', '>', Carbon::now())
            ->where('expiration_date', '<=', Carbon::now()->addMonths(3))
            ->get()
            ->map(function ($product) {
                $daysUntilExpiration = Carbon::now()->diffInDays($product->expiration_date, false);
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'expiration_date' => $product->expiration_date->format('d/m/Y'),
                    'days_until_expiration' => (int) $daysUntilExpiration,
                    'alert_type' => 'expiring_soon'
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'totals' => [
                'sales' => [
                    'amount' => 'S/ ' . number_format((float)$totalSales, 2),
                    'percentage' => $salesPercentage,
                    'trend' => $salesPercentage >= 0 ? 'up' : 'down'
                ],
                'products' => [
                    'count' => number_format((int)$totalProducts),
                    'new' => $newProducts
                ],
                'customers' => [
                    'count' => number_format((int)$totalCustomers),
                    'new' => $newCustomers
                ],
                'categories' => [
                    'count' => number_format((int)$totalCategories),
                    'new' => $newCategories
                ]
            ],
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
            'salesByDay' => $salesByDay,
            'alerts' => [
                'lowStock' => $lowStockProducts,
                'expiring' => $expiringProducts
            ]
        ]);
    }
} 