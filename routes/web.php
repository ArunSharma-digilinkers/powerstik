<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Token-authenticated (see OpsController); CSRF is exempted in bootstrap/app.php.
Route::post('_ops/deploy', [OpsController::class, 'deploy'])->middleware('throttle:5,1');
