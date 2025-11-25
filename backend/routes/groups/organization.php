<?php

use App\Domain\Organization\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

//Route::middleware('tenant')->group(function () {
    Route::get('organization', [OrganizationController::class, 'show']);
    Route::put('organization', [OrganizationController::class, 'update']);
//});
