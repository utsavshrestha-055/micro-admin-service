<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IntegrationPlaceholderController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
Route::get('/product-images/{imageId}', [ProductController::class, 'showImage'])
    ->whereUuid('imageId')
    ->name('product-images.show');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)
        ->middleware('permission:admin.dashboard.view')
        ->name('dashboard');

    Route::prefix('users')->name('users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view_all')->name('index');
        Route::get('/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::get('/{userId}', [UserController::class, 'show'])
            ->whereUuid('userId')
            ->middleware(['permission:users.read', 'permission:roles.manage'])
            ->name('show');
        Route::put('/{userId}', [UserController::class, 'update'])->whereUuid('userId')->middleware('permission:users.update')->name('update');
        Route::delete('/{userId}', [UserController::class, 'destroy'])->whereUuid('userId')->middleware('permission:users.delete')->name('destroy');
        Route::post('/{userId}/roles', [UserController::class, 'assignRole'])
            ->whereUuid('userId')
            ->middleware(['permission:users.read', 'permission:roles.manage', 'permission:roles.assign'])
            ->name('roles.assign');
        Route::delete('/{userId}/roles', [UserController::class, 'unassignRole'])
            ->whereUuid('userId')
            ->middleware(['permission:users.read', 'permission:roles.manage', 'permission:roles.assign'])
            ->name('roles.unassign');
    });

    Route::prefix('roles')->name('roles.')->middleware('permission:roles.manage')->group(function (): void {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{roleId}', [RoleController::class, 'show'])
            ->whereUuid('roleId')
            ->middleware('permission:permissions.manage')
            ->name('show');
        Route::put('/{roleId}', [RoleController::class, 'update'])->whereUuid('roleId')->name('update');
        Route::delete('/{roleId}', [RoleController::class, 'destroy'])->whereUuid('roleId')->name('destroy');
        Route::post('/{roleId}/permissions', [RoleController::class, 'grantPermission'])
            ->whereUuid('roleId')
            ->middleware('permission:permissions.manage')
            ->name('permissions.grant');
        Route::delete('/{roleId}/permissions', [RoleController::class, 'revokePermission'])
            ->whereUuid('roleId')
            ->middleware('permission:permissions.manage')
            ->name('permissions.revoke');
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:permissions.manage')->group(function (): void {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::post('/seed', [PermissionController::class, 'seed'])->name('seed');
        Route::put('/{permissionId}', [PermissionController::class, 'update'])->whereUuid('permissionId')->name('update');
        Route::delete('/{permissionId}', [PermissionController::class, 'destroy'])->whereUuid('permissionId')->name('destroy');
    });

    Route::prefix('products')->name('products.')->group(function (): void {
        Route::get('/', [ProductController::class, 'index'])->middleware(['permission:products.view_all', 'permission:categories.view_all'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->middleware(['permission:products.create', 'permission:categories.view_all'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.create')->name('store');
        Route::get('/{productId}/edit', [ProductController::class, 'edit'])->whereUuid('productId')->middleware(['permission:products.update', 'permission:products.read', 'permission:categories.view_all'])->name('edit');
        Route::put('/{productId}', [ProductController::class, 'update'])->whereUuid('productId')->middleware('permission:products.update')->name('update');
        Route::delete('/{productId}', [ProductController::class, 'destroy'])->whereUuid('productId')->middleware('permission:products.delete')->name('destroy');
        Route::put('/{productId}/details', [ProductController::class, 'saveDetails'])->whereUuid('productId')->middleware(['permission:products.update', 'permission:products.read'])->name('details.update');
        Route::delete('/{productId}/details', [ProductController::class, 'deleteDetails'])->whereUuid('productId')->middleware('permission:products.update')->name('details.destroy');
        Route::post('/{productId}/images', [ProductController::class, 'storeImage'])->whereUuid('productId')->middleware('permission:products.update')->name('images.store');
        Route::put('/{productId}/images/{imageId}', [ProductController::class, 'updateImage'])->whereUuid('productId')->whereUuid('imageId')->middleware('permission:products.update')->name('images.update');
        Route::delete('/{productId}/images/{imageId}', [ProductController::class, 'deleteImage'])->whereUuid('productId')->whereUuid('imageId')->middleware('permission:products.update')->name('images.destroy');
    });

    Route::prefix('categories')->name('categories.')->group(function (): void {
        Route::get('/', [CategoryController::class, 'index'])->middleware('permission:categories.view_all')->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->middleware(['permission:categories.create', 'permission:categories.view_all'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:categories.create')->name('store');
        Route::get('/{categoryId}/edit', [CategoryController::class, 'edit'])->whereUuid('categoryId')->middleware(['permission:categories.update', 'permission:categories.read'])->name('edit');
        Route::put('/{categoryId}', [CategoryController::class, 'update'])->whereUuid('categoryId')->middleware('permission:categories.update')->name('update');
        Route::delete('/{categoryId}', [CategoryController::class, 'destroy'])->whereUuid('categoryId')->middleware('permission:categories.delete')->name('destroy');
    });

    Route::prefix('orders')->name('orders.')->group(function (): void {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.view_all')->name('index');
        Route::get('/{orderId}', [OrderController::class, 'show'])->whereUuid('orderId')->middleware('permission:orders.read')->name('show');
        Route::put('/{orderId}/status', [OrderController::class, 'updateStatus'])->whereUuid('orderId')->middleware('permission:orders.update')->name('status');
    });

    $sections = [
        'merchants' => ['merchants.read', 'merchants'],
        'customers' => ['customers.read', 'customers'],
        'inventory' => ['inventory.read', 'inventory'],
        'reports' => ['reports.read', 'reports'],
        'settings' => ['settings.manage', 'settings'],
        'audit-logs' => ['audit_logs.read', 'audit-logs'],
    ];

    foreach ($sections as $section => [$permission, $routeName]) {
        Route::get('/'.$routeName, IntegrationPlaceholderController::class)
            ->defaults('section', $section)
            ->middleware('permission:'.$permission)
            ->name($routeName.'.index');
    }
});
