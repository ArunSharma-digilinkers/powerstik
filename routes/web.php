<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BatteryLabelsController;
use App\Http\Controllers\CapabilityController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\OpsController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\WorkController;
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

// resources/downloads is declared before the file route so the slugs cannot collide
Route::get('resources', [ResourcesController::class, 'index'])->name('resources.index');
Route::get('resources/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::post('resources/downloads/{download}', [DownloadController::class, 'request'])
    ->middleware('throttle:10,1')->name('downloads.request');
Route::get('resources/downloads/{download}/file', [DownloadController::class, 'file'])->name('downloads.file');
Route::get('resources/faqs', [ResourcesController::class, 'faqs'])->name('resources.faqs');
Route::get('resources/material-guide', [ResourcesController::class, 'materialGuide'])->name('resources.material-guide');

Route::get('insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('insights/{post}', [InsightController::class, 'show'])->name('insights.show');

Route::get('work', [WorkController::class, 'index'])->name('work.index');
Route::get('work/{project}', [WorkController::class, 'show'])->name('work.show');

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

// Phase 2 (PLAN.md §3). The utility bar and footer link here, so it is a real
// placeholder page rather than a 404 until the client zone is built.
Route::view('client/login', 'client.login')->name('client.login');

// Token-authenticated (see OpsController); CSRF is exempted in bootstrap/app.php.
Route::post('_ops/deploy', [OpsController::class, 'deploy'])->middleware('throttle:5,1');
