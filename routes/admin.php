<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SystemController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::middleware('admin:'.User::ROLE_SALES)->group(function () {
            Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
            Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
            Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
            Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
            Route::get('leads/{lead}/files/{file}', [LeadController::class, 'file'])->scopeBindings()->name('leads.file');
        });

        foreach (config('admin.resources') as $slug => [$controller, , , $roles]) {
            Route::resource($slug, $controller)
                ->except('show')
                ->parameters([$slug => 'id'])
                ->middleware('admin:'.implode(',', $roles));
        }

        Route::middleware('admin:'.User::ROLE_ADMIN)->group(function () {
            Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
            Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::get('system', [SystemController::class, 'index'])->name('system.index');
            Route::post('system', [SystemController::class, 'run'])->name('system.run');
        });
    });
});
