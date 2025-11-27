<?php

use App\Domain\Reference\Controllers\ReferenceController;
use Illuminate\Support\Facades\Route;

Route::prefix('reference')->group(function () {
    Route::get('units', [ReferenceController::class, 'units']);
});
