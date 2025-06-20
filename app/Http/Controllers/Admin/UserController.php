<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role ?? 'employee',
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'profile_photo_url' => $user->profile_photo_url,
                'formatted_id' => 'U' . str_pad($user->id, 4, '0', STR_PAD_LEFT)
            ];
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,employee'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now()
        ]);

        return redirect()->route('admin.users.index')
            ->with('message', 'Usuario creado exitosamente')
            ->with('type', 'success');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,employee'
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('message', 'Usuario actualizado exitosamente')
            ->with('type', 'success');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'No puedes eliminar el último administrador');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('message', 'Usuario eliminado exitosamente')
            ->with('type', 'success');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles);

        return back()->with('message', 'Roles actualizados correctamente');
    }

    public function approve(User $user)
    {
        $user->email_verified_at = now();
        $user->save();

        return back()->with('message', 'Usuario autorizado correctamente');
    }

    public function reject(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        if ($user->roles->contains('slug', 'admin') && User::whereHas('roles', function($query) {
            $query->where('slug', 'admin');
        })->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el último administrador');
        }

        $user->delete();

        return back()->with('message', 'Usuario eliminado correctamente');
    }

    public function showSales(User $user)
    {
        $sales = $user->sales()
            ->with(['items.product'])
            ->latest()
            ->paginate(10)
            ->through(function ($sale) {
                return [
                    'id' => $sale->id,
                    'total' => number_format($sale->total, 2),
                    'items_count' => $sale->items->count(),
                    'created_at' => $sale->created_at->format('d/m/Y H:i'),
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => number_format($item->price, 2),
                            'subtotal' => number_format($item->quantity * $item->price, 2)
                        ];
                    })
                ];
            });

        return Inertia::render('Admin/Users/Sales', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'sales' => $sales
        ]);
    }

    public function sales(User $user)
    {
        $statusTranslations = [
            'completed' => 'completada',
            'pending' => 'pendiente',
            'cancelled' => 'cancelada'
        ];

        $sales = $user->sales()
            ->with(['items.product'])
            ->latest()
            ->get()
            ->map(function ($sale) use ($statusTranslations) {
                return [
                    'id' => $sale->id,
                    'created_at' => $sale->created_at,
                    'total' => 'S/ ' . number_format($sale->total, 2),
                    'status' => $statusTranslations[$sale->status] ?? $sale->status,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'product' => [
                                'name' => $item->product->name
                            ]
                        ];
                    })
                ];
            });

        return response()->json([
            'sales' => $sales
        ]);
    }

    public function salesReport(User $user)
    {
        $sales = $user->sales()
            ->with(['items.product'])
            ->latest()
            ->get();

        $totalSales = $sales->count();
        $totalAmount = $sales->sum('total');

        return view('admin.users.sales-report', [
            'user' => $user,
            'sales' => $sales,
            'totalSales' => $totalSales,
            'totalAmount' => $totalAmount,
            'generatedAt' => now()->format('d/m/Y H:i:s')
        ]);
    }

    public function show(User $user)
    {
        // Obtener las ventas del usuario con sus items y productos
        $sales = $user->sales()
            ->with(['items.product', 'client'])
            ->latest()
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'code' => $sale->code ?? 'V' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                    'total' => $sale->total,
                    'status' => $sale->status,
                    'status_label' => $sale->status === 'completed' ? 'completada' : 
                                    ($sale->status === 'pending' ? 'pendiente' : 'cancelada'),
                    'created_at' => $sale->created_at->format('d/m/Y H:i'),
                    'items_count' => $sale->items->count(),
                    'client' => $sale->client ? [
                        'full_name' => $sale->client->full_name,
                        'dni' => $sale->client->dni
                    ] : null,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_name' => $item->product ? $item->product->name : 'Producto eliminado',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->quantity * $item->price
                        ];
                    })
                ];
            });

        // Calcular estadísticas
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $completedSales = $sales->where('status', 'completed')->count();
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return Inertia::render('Admin/Users/Show', [
            'user' => [
                'id' => $user->id,
                'formatted_id' => 'U' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->role === 'admin' ? 'Administrador' : 'Empleado',
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'email_verified_at' => $user->email_verified_at,
            ],
            'sales' => $sales,
            'statistics' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'completed_sales' => $completedSales,
                'average_sale' => $averageSale
            ]
        ]);
    }

    public function getUserData(User $user)
    {
        // Obtener las ventas del usuario con sus items y productos
        $sales = $user->sales()
            ->with(['items.product', 'client'])
            ->latest()
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'code' => $sale->code ?? 'V' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                    'total' => $sale->total,
                    'status' => $sale->status,
                    'status_label' => $sale->status === 'completed' ? 'completada' : 
                                    ($sale->status === 'pending' ? 'pendiente' : 'cancelada'),
                    'created_at' => $sale->created_at->format('d/m/Y H:i'),
                    'items_count' => $sale->items->count(),
                    'client' => $sale->client ? [
                        'full_name' => $sale->client->full_name,
                        'dni' => $sale->client->dni
                    ] : null,
                ];
            });

        // Calcular estadísticas
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $completedSales = $sales->where('status', 'completed')->count();
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'formatted_id' => 'U' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_label' => $user->role === 'admin' ? 'Administrador' : 'Empleado',
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'email_verified_at' => $user->email_verified_at,
            ],
            'sales' => $sales,
            'statistics' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'completed_sales' => $completedSales,
                'average_sale' => $averageSale
            ]
        ]);
    }
} 