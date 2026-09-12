<?php

declare(strict_types=1);

use App\Http\Controllers\Account\AccountGetController;
use App\Http\Controllers\Account\AccountPostController;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt.auth')->prefix('')->group(function () {
    Route::post('account', AccountPostController::class);
    Route::get('account/{id}', AccountGetController::class);
});
