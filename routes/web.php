<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Employee\ProductController as EmployeeProductController;
use App\Http\Controllers\Employee\SaleController as EmployeeSaleController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard unificado - accesible para todos los usuarios autenticados
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Rutas exclusivas de admin
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        // Dashboard de admin (redirige al dashboard unificado)
        Route::get('/dashboard', function () {
            return redirect()->route('dashboard');
        })->name('dashboard');

        // Categories
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        
        // Brands
        Route::resource('brands', BrandController::class);
        Route::post('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
        
        // Suppliers
        Route::resource('suppliers', SupplierController::class);
        Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
        
        // Products
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        
        // Users
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/data', [UserController::class, 'getUserData'])->name('users.data');
        Route::post('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::delete('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::get('/users/{user}/sales', [UserController::class, 'showSales'])->name('users.sales');
        Route::get('/users/{user}/sales-data', [UserController::class, 'sales'])->name('users.sales-data');
        Route::get('/users/{user}/sales-report', [UserController::class, 'salesReport'])->name('users.sales-report');

        // Clients
        Route::resource('clients', ClientController::class);
        Route::post('/clients/search-dni', [ClientController::class, 'searchByDni'])->name('clients.search-dni');
        Route::post('/clients/create-from-sale', [ClientController::class, 'createFromSale'])->name('clients.create-from-sale');

        // Sales
        Route::resource('sales', SaleController::class);
        Route::get('sales/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');
    });
});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de empleado
Route::middleware(['auth', 'role:employee'])->name('employee.')->prefix('employee')->group(function () {
    // Dashboard de empleado (redirige al dashboard unificado)
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('dashboard');
    
    // Products (solo lectura)
    Route::get('/products', [EmployeeProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [EmployeeProductController::class, 'show'])->name('products.show');
    
    // Sales (lectura + crear + imprimir)
    Route::get('/sales', [EmployeeSaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [EmployeeSaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [EmployeeSaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [EmployeeSaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/ticket', [EmployeeSaleController::class, 'ticket'])->name('sales.ticket');
    
    // Búsqueda de clientes para empleados
    Route::post('/clients/search-dni', [ClientController::class, 'searchByDni'])->name('clients.search-dni');
    
    // Profile (solo lectura)
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
});

require __DIR__.'/auth.php';
