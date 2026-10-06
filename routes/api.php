<?php

use App\Http\Controllers\Api\TemplateApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API (read-only)
|--------------------------------------------------------------------------
*/

Route::middleware(['central', 'throttle:api'])->group(function () {
    Route::get('/templates', [TemplateApiController::class, 'index'])->name('api.templates.index');
    Route::get('/subdomain-availability', [TemplateApiController::class, 'subdomainAvailability'])->name('api.subdomain');
});
