<?php

use App\Http\Controllers\Auth\LoginPostController;
use Illuminate\Support\Facades\Route;

// non-authenticated routes
Route::post('login', [LoginPostController::class, '__invoke']);

// authenticated routes
Route::prefix('api')->group(function () {
    require_once __DIR__.'/auth/routes.php';
})->middleware('jwt.auth');