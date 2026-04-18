<?php

use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FoodCategoryController as AdminFoodCategoryController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\VisitSessionController as AdminVisitSessionController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/home');
Route::get('/home', [PublicPageController::class, 'home'])->name('home');
Route::get('/gioi-thieu', [PublicPageController::class, 'about'])->name('about');
Route::get('/thuc-pham', [CatalogController::class, 'index'])->name('foods.index');
Route::get('/thuc-pham/{food:slug}', [CatalogController::class, 'show'])->name('foods.show');
Route::redirect('/dashboard', '/home')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/history', [OrderController::class, 'history'])->name('history');
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('throttle:orders')
        ->name('orders.store');
});

Route::get('/chat', [ChatController::class, 'show'])->name('chat.show');
Route::get('/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
Route::post('/chat/send', [ChatController::class, 'send'])
    ->middleware('throttle:chat-send')
    ->name('chat.send');
Route::post('/chat/upload-image', [ChatController::class, 'uploadImage'])
    ->middleware('throttle:chat-upload')
    ->name('chat.upload-image');

Route::middleware('guest')->group(function () {
    Route::get('/adminlogin', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:admin-login')
        ->name('admin.login.store');
});

Route::prefix('/admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/foods', [AdminFoodController::class, 'index'])->name('admin.foods.index');
    Route::post('/foods', [AdminFoodController::class, 'store'])->name('admin.foods.store');
    Route::get('/foods/{food}/edit', [AdminFoodController::class, 'edit'])->name('admin.foods.edit');
    Route::put('/foods/{food}', [AdminFoodController::class, 'update'])->name('admin.foods.update');
    Route::delete('/foods/{food}', [AdminFoodController::class, 'destroy'])->name('admin.foods.destroy');

    Route::get('/categories', [AdminFoodCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [AdminFoodCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminFoodCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminFoodCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminFoodCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');

    Route::get('/visit-sessions', [AdminVisitSessionController::class, 'index'])->name('admin.visit-sessions.index');
    Route::post('/visit-sessions', [AdminVisitSessionController::class, 'store'])->name('admin.visit-sessions.store');
    Route::post('/visit-sessions/{visitSession}/disable', [AdminVisitSessionController::class, 'disable'])->name('admin.visit-sessions.disable');
    Route::delete('/visit-sessions/{visitSession}', [AdminVisitSessionController::class, 'destroy'])->name('admin.visit-sessions.destroy');

    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/chat/{session}/reply', [AdminChatController::class, 'reply'])->name('admin.chat.reply');
    Route::post('/chat/{session}/close', [AdminChatController::class, 'close'])->name('admin.chat.close');
    Route::delete('/chat/{session}', [AdminChatController::class, 'destroy'])->name('admin.chat.destroy');
    Route::delete('/chat/{session}/messages/{message}', [AdminChatController::class, 'destroyMessage'])->name('admin.chat.messages.destroy');
});

require __DIR__.'/auth.php';
