<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('about', AboutController::class)->name('about');
Route::permanentRedirect('about-us', '/about');

// Token-authenticated (see OpsController); CSRF is exempted in bootstrap/app.php.
Route::post('_ops/deploy', [OpsController::class, 'deploy'])->middleware('throttle:5,1');
