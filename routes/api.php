<?php

use App\Http\Controllers\Api\FoodController as ApiFoodController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - AJAX Endpoints
|--------------------------------------------------------------------------
| Dựa trên everything-claude-code skills: api-design, laravel-patterns
| - REST conventions
| - JSON responses với consistent structure
| - Rate limiting và validation
| - CSRF protection cho non-API calls
*/

Route::middleware(['throttle:api'])->group(function () {
    // Foods API - Server-rendered hoặc JSON tùy Accept header
    Route::get('/foods', [ApiFoodController::class, 'index'])->name('api.foods.index');
    Route::get('/foods/{food:slug}', [ApiFoodController::class, 'show'])->name('api.foods.show');

    // Orders API - AJAX order placement
    Route::post('/orders', [ApiOrderController::class, 'store'])->name('api.orders.store');
});
