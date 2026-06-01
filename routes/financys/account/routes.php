<?php

declare(strict_types = 1);

use App\Http\Controllers\Account\AccountPostController;

Route::prefix('')->group(function () {
    Route::post('account', [AccountPostController::class, '__invoke']);
})->middleware('jwt.auth');