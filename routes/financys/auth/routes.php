<?php

declare(strict_types = 1);

use App\Http\Controllers\Auth\LoginPostController;
use Illuminate\Support\Facades\Route;

// non-authenticated routes
Route::post('login', LoginPostController::class);