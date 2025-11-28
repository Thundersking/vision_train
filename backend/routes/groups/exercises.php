<?php

use App\Domain\Exercise\Controllers\ExerciseController;
use Illuminate\Support\Facades\Route;

Route::apiResource('exercises', ExerciseController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::get('patients/{uuid}/exercises', [ExerciseController::class, 'indexByPatient']);

