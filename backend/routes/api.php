<?php

use App\Application\Api\UserController;
use Illuminate\Support\Facades\Route;

// TODO: разобраться с тенантами
//Route::middleware(['tenant'])->group(function () {
//});

Route::get('/user', [UserController::class, 'index']);
