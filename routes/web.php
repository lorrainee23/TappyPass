<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

// Admin routes
Route::prefix('admin')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Protected admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Routes
        Route::get('/routes', [\App\Http\Controllers\Admin\RouteController::class, 'index'])->name('admin.routes.index');
        Route::get('/routes/create', [\App\Http\Controllers\Admin\RouteController::class, 'create'])->name('admin.routes.create');
        Route::post('/routes', [\App\Http\Controllers\Admin\RouteController::class, 'store'])->name('admin.routes.store');
        Route::get('/routes/{id}/edit', [\App\Http\Controllers\Admin\RouteController::class, 'edit'])->name('admin.routes.edit');
        Route::put('/routes/{id}', [\App\Http\Controllers\Admin\RouteController::class, 'update'])->name('admin.routes.update');
        Route::delete('/routes/{id}', [\App\Http\Controllers\Admin\RouteController::class, 'destroy'])->name('admin.routes.destroy');
        Route::post('/routes/{id}/toggle-status', [\App\Http\Controllers\Admin\RouteController::class, 'toggleStatus'])->name('admin.routes.toggle-status');
        
        // Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('admin.bookings.show');
        Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('admin.bookings.confirm');
        Route::post('/bookings/{id}/payment-status', [BookingController::class, 'updatePaymentStatus'])->name('admin.bookings.payment-status');
        
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings/gcash-qr', [SettingController::class, 'updateGcashQr'])->name('admin.settings.gcash-qr');
    });
});
