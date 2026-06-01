<?php

declare(strict_types = 1);

use App\Http\Controllers\Auth\LoginPostController;

// non-authenticated routes
Route::post('login', [LoginPostController::class, '__invoke']);