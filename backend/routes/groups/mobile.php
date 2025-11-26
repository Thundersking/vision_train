<?php

use App\Domain\Patient\Controllers\MobileConnectionTokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('connection-tokens/{token}', [MobileConnectionTokenController::class, 'store'])
        ->name('mobile.connection-tokens.activate');
});
