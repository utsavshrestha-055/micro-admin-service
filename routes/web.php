<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IntegrationPlaceholderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)
        ->middleware('permission:admin.dashboard.view')
        ->name('dashboard');

    Route::prefix('users')->name('users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.read')->name('index');
        Route::get('/{userId}', [UserController::class, 'show'])
            ->whereUuid('userId')
            ->middleware(['permission:users.read', 'permission:roles.manage'])
            ->name('show');
        Route::put('/{userId}', [UserController::class, 'update'])->whereUuid('userId')->middleware('permission:users.manage')->name('update');
        Route::delete('/{userId}', [UserController::class, 'destroy'])->whereUuid('userId')->middleware('permission:users.manage')->name('destroy');
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
        Route::put('/{permissionId}', [PermissionController::class, 'update'])->whereUuid('permissionId')->name('update');
        Route::delete('/{permissionId}', [PermissionController::class, 'destroy'])->whereUuid('permissionId')->name('destroy');
    });

    $sections = [
        'products' => ['products.read', 'products'],
        'categories' => ['categories.read', 'categories'],
        'merchants' => ['merchants.read', 'merchants'],
        'orders' => ['orders.read', 'orders'],
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
