<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/Asset_Travelo/{path}', function ($path) {
    $fullPath = public_path('Asset_Travelo/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*');


// ROUTE AUTENTIKASI (Tanpa login - Hanya Login, Tidak Ada Register)
Route::get('admin/login', [AuthAdminController::class, 'login'])->name('login');
Route::post('admin/login', [AuthAdminController::class, 'loginAction'])->name('loginAction');
Route::post('admin/logout', [AuthAdminController::class, 'logout'])->name('logout');

// ROUTE YANG MEMERLUKAN AUTHENTIKASI DAN ROLE ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard - Hanya bisa diakses oleh admin
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Resource routes lainnya dengan prefix admin untuk menghindari conflict dengan static files
    Route::resource('users', UserController::class);
    Route::resource('destinations', DestinationController::class);
    Route::resource('bookings', BookingController::class);
});
