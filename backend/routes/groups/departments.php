<?php

use App\Domain\Department\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

//Route::middleware('tenant')->group(function () {
    Route::apiResource('departments', DepartmentController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy']);
//});
