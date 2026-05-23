<?php

use App\Http\Controllers\LoginPostController;
use Illuminate\Support\Facades\Route;

// non-authenticated routes
Route::prefix('api')->group(function () {
    Route::post('/login', [LoginPostController::class, '__invoke']);
});

// authenticated routes
Route::prefix('api')->group(function () {
    require_once './auth/routes.php';
})->middleware('jwt.auth');