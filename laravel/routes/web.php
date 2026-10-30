<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->landingRoute())
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    Route::get('/dashboard', [WarehouseController::class, 'dashboard'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/reports', [WarehouseController::class, 'reports'])->middleware('permission:reports.view')->name('reports.index');
    Route::get('/reports/export', [WarehouseController::class, 'exportReport'])->middleware('permission:reports.view')->name('reports.export');

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/', [WarehouseController::class, 'devices'])->middleware('permission:devices.view')->name('index');
        Route::get('/export', [WarehouseController::class, 'exportDevices'])->middleware('permission:devices.export')->name('export');
        Route::get('/create', [WarehouseController::class, 'createDevice'])->middleware('permission:devices.create')->name('create');
        Route::post('/', [WarehouseController::class, 'storeDevice'])->middleware('permission:devices.create')->name('store');
        Route::get('/{id}/edit', [WarehouseController::class, 'editDevice'])->whereNumber('id')->middleware('permission:devices.update')->name('edit');
        Route::put('/{id}', [WarehouseController::class, 'updateDevice'])->whereNumber('id')->middleware('permission:devices.update')->name('update');
        Route::delete('/{id}', [WarehouseController::class, 'deleteDevice'])->whereNumber('id')->middleware('permission:devices.delete')->name('destroy');
        Route::get('/{id}', [WarehouseController::class, 'showDevice'])->whereNumber('id')->middleware('permission:devices.view')->name('show');
    });

    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [WarehouseController::class, 'departments'])->middleware('permission:departments.view')->name('index');
        Route::post('/', [WarehouseController::class, 'storeDepartment'])->middleware('permission:departments.create')->name('store');
        Route::put('/{id}', [WarehouseController::class, 'updateDepartment'])->whereNumber('id')->middleware('permission:departments.update')->name('update');
        Route::delete('/{id}', [WarehouseController::class, 'deleteDepartment'])->whereNumber('id')->middleware('permission:departments.delete')->name('destroy');
    });

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [WarehouseController::class, 'employees'])->middleware('permission:employees.view')->name('index');
        Route::post('/', [WarehouseController::class, 'storeEmployee'])->middleware('permission:employees.create')->name('store');
        Route::get('/{id}', [WarehouseController::class, 'showEmployee'])->whereNumber('id')->middleware('permission:employees.view')->name('show');
        Route::put('/{id}', [WarehouseController::class, 'updateEmployee'])->whereNumber('id')->middleware('permission:employees.update')->name('update');
        Route::post('/{id}/bulk-revoke', [WarehouseController::class, 'bulkRevokeEmployee'])->whereNumber('id')->middleware('permission:employees.revoke')->name('bulk-revoke');
        Route::delete('/{id}', [WarehouseController::class, 'deleteEmployee'])->whereNumber('id')->middleware('permission:employees.delete')->name('destroy');
    });

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [WarehouseController::class, 'tickets'])->middleware('permission:tickets.view')->name('index');
        Route::get('/create', [WarehouseController::class, 'createTicket'])->middleware('permission:tickets.create')->name('create');
        Route::post('/', [WarehouseController::class, 'storeTicket'])->middleware('permission:tickets.create')->name('store');
        Route::get('/{id}/print', [WarehouseController::class, 'printTicket'])->whereNumber('id')->middleware('permission:tickets.print')->name('print');
        Route::get('/{id}', [WarehouseController::class, 'showTicket'])->whereNumber('id')->middleware('permission:tickets.view')->name('show');
    });

    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [WarehouseController::class, 'maintenance'])->middleware('permission:maintenance.view')->name('index');
        Route::post('/', [WarehouseController::class, 'storeMaintenance'])->middleware('permission:maintenance.create')->name('store');
        Route::put('/{id}', [WarehouseController::class, 'updateMaintenance'])->whereNumber('id')->middleware('permission:maintenance.update')->name('update');
    });

    Route::middleware('permission:users.manage')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::put('/{id}', [AdminController::class, 'updateUser'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AdminController::class, 'deactivateUser'])->whereNumber('id')->name('destroy');
    });

    Route::middleware('permission:roles.manage')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [AdminController::class, 'roles'])->name('index');
        Route::post('/', [AdminController::class, 'storeRole'])->name('store');
        Route::put('/{id}/permissions', [AdminController::class, 'updateRolePermissions'])->whereNumber('id')->name('permissions');
        Route::delete('/{id}', [AdminController::class, 'deleteRole'])->whereNumber('id')->name('destroy');
    });
});
