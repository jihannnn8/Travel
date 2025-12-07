<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\SlidersController;
use App\Http\Controllers\Api\PromosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Destinations routes (public - bisa dilihat tanpa login)
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{id}', [DestinationController::class, 'show']);

// Cities routes (public - bisa dilihat tanpa login)
Route::get('/cities', [CitiesController::class, 'index']);
Route::get('/cities/{id}', [CitiesController::class, 'show']);

// Sliders routes (public - bisa dilihat tanpa login)
Route::get('/sliders', [SlidersController::class, 'index']);
Route::get('/sliders/{id}', [SlidersController::class, 'show']);

// Promos routes (public - bisa dilihat tanpa login)
Route::get('/promos', [PromosController::class, 'index']);
Route::get('/promos/{id}', [PromosController::class, 'show']);

// Midtrans notification callback (public - no auth required)
Route::post('/payment/notification', [BookingController::class, 'notification']);

// Image serving routes (public - no auth required)
// Route untuk serve images dari Asset_Travelo (seeder images)
Route::get('/asset/{path}', function ($path) {
    try {
        // Normalize path - remove leading/trailing slashes and prevent path traversal
        $path = trim($path, '/');
        $path = str_replace('..', '', $path); // Prevent directory traversal
        
        $fullPath = public_path('Asset_Travelo/' . $path);
        
        // Security check: ensure file is within Asset_Travelo directory
        $assetBasePath = realpath(public_path('Asset_Travelo'));
        $realFullPath = realpath($fullPath);
        
        if (!$realFullPath || strpos($realFullPath, $assetBasePath) !== 0) {
            \Log::warning("Invalid asset path requested: $path");
            abort(404, 'File not found');
        }
        
        if (!file_exists($realFullPath) || !is_file($realFullPath)) {
            \Log::warning("Asset file not found: $path");
            abort(404, 'File not found');
        }
        
        // Get MIME type
        $mimeType = mime_content_type($realFullPath);
        if (!$mimeType) {
            // Try to determine from extension
            $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'image/jpeg';
        }
        
        // Set CORS headers dan content type
        $response = response()->file($realFullPath, [
            'Content-Type' => $mimeType,
        ]);
        
        // Add CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        
        return $response;
    } catch (\Exception $e) {
        \Log::error("Error serving asset: {$e->getMessage()}", [
            'path' => $path,
            'trace' => $e->getTraceAsString()
        ]);
        abort(500, 'Internal server error');
    }
})->where('path', '.*');

// Route untuk serve images dari storage (uploaded images)
Route::get('/storage/{path}', function ($path) {
    try {
        // Normalize path - remove leading/trailing slashes and prevent path traversal
        $path = trim($path, '/');
        $path = str_replace('..', '', $path); // Prevent directory traversal
        
        // Cek apakah storage link ada, jika tidak gunakan storage_path langsung
        $publicStoragePath = public_path('storage/' . $path);
        $appStoragePath = storage_path('app/public/' . $path);
        
        // Cek di public/storage (symlink) terlebih dahulu
        $fullPath = null;
        if (file_exists($publicStoragePath) && is_file($publicStoragePath)) {
            $fullPath = $publicStoragePath;
        } 
        // Jika tidak ada, cek di storage/app/public (actual storage)
        elseif (file_exists($appStoragePath) && is_file($appStoragePath)) {
            $fullPath = $appStoragePath;
        }
        
        if (!$fullPath) {
            \Log::warning("Storage file not found: $path (checked: $publicStoragePath and $appStoragePath)");
            abort(404, 'File not found');
        }
        
        // Security check: ensure file is within storage directory
        $storageBasePath = realpath(storage_path('app/public'));
        $publicBasePath = realpath(public_path('storage'));
        $realFullPath = realpath($fullPath);
        
        if (!$realFullPath) {
            \Log::warning("Cannot resolve realpath for: $fullPath");
            abort(404, 'File not found');
        }
        
        // Cek apakah file dalam storage/app/public atau public/storage
        $isInStorage = $storageBasePath && strpos($realFullPath, $storageBasePath) === 0;
        $isInPublic = $publicBasePath && strpos($realFullPath, $publicBasePath) === 0;
        
        if (!$isInStorage && !$isInPublic) {
            \Log::warning("Invalid storage path requested: $path (resolved to: $realFullPath)");
            abort(404, 'File not found');
        }
        
        // Get MIME type
        $mimeType = mime_content_type($realFullPath);
        if (!$mimeType) {
            // Try to determine from extension
            $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'image/jpeg';
        }
        
        // Set CORS headers dan content type
        $response = response()->file($realFullPath, [
            'Content-Type' => $mimeType,
        ]);
        
        // Add CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        
        return $response;
    } catch (\Exception $e) {
        \Log::error("Error serving storage file: {$e->getMessage()}", [
            'path' => $path,
            'public_path' => $publicStoragePath ?? 'N/A',
            'app_path' => $appStoragePath ?? 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return error response dengan detail untuk debugging
        return response()->json([
            'error' => 'Failed to serve storage file',
            'message' => $e->getMessage(),
            'path' => $path,
        ], 500);
    }
})->where('path', '.*');

// Protected routes (authentication required - using Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::patch('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => Auth::user(),
            ],
        ]);
    });
    
    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::get('/bookings/{id}/status', [BookingController::class, 'checkStatus']);
});