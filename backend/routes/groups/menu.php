<?php

use App\Domain\Menu\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

//Route::apiResource('users', MenuController::class)->only(['index'])->middleware('auth:sanctum');
Route::apiResource('menu', MenuController::class)->only(['index']);
