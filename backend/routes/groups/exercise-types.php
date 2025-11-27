<?php

use App\Domain\ExerciseType\Controllers\ExerciseTypeController;
use Illuminate\Support\Facades\Route;

Route::get('exercise-types/all-list', [ExerciseTypeController::class, 'allList']);
Route::apiResource('exercise-types', ExerciseTypeController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
