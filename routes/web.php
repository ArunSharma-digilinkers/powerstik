<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BatteryLabelsController;
use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\OpsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('about', AboutController::class)->name('about');
Route::permanentRedirect('about-us', '/about');
Route::get('battery-labels', BatteryLabelsController::class)->name('battery-labels');

// machine-park is declared before the wildcard so it is not read as a discipline
Route::get('capabilities', [CapabilityController::class, 'index'])->name('capabilities.index');
Route::get('capabilities/machine-park', [CapabilityController::class, 'machines'])->name('capabilities.machines');
Route::get('capabilities/{capability}', [CapabilityController::class, 'show'])->name('capabilities.show');

Route::get('global', GlobalController::class)->name('global');

Route::get('industries', [IndustryController::class, 'index'])->name('industries.index');
Route::get('industries/{industry}', [IndustryController::class, 'show'])->name('industries.show');

// Conversion pages. The three wizard forms share one controller action; the
// route default picks which set of steps EnquiryController::INTENTS renders.
Route::get('get-a-quote', [EnquiryController::class, 'form'])->defaults('intent', 'quote')->name('quote');
Route::get('request-a-sample', [EnquiryController::class, 'form'])->defaults('intent', 'sample')->name('sample');
Route::get('international-enquiry', [EnquiryController::class, 'form'])->defaults('intent', 'international')->name('international');
Route::get('contact', [EnquiryController::class, 'contact'])->name('contact');

Route::post('enquiry/{type}', [EnquiryController::class, 'store'])
    ->whereIn('type', ['quote', 'sample', 'international', 'contact', 'callback'])
    ->middleware('throttle:8,1')
    ->name('enquiry.store');

Route::get('thank-you/{type}', [EnquiryController::class, 'thanks'])
    ->whereIn('type', ['quote', 'sample', 'international', 'contact', 'callback'])
    ->name('thanks');

// Token-authenticated (see OpsController); CSRF is exempted in bootstrap/app.php.
Route::post('_ops/deploy', [OpsController::class, 'deploy'])->middleware('throttle:5,1');
