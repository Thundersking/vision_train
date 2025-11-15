<?php

use App\Domain\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'update'])->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->only(['index', 'show', 'store', 'update']);
Route::patch('users/{uuid}/toggle-status', [UserController::class, 'toggleStatus']);
