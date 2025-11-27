<?php

use App\Domain\ExerciseTemplate\Controllers\ExerciseTemplateController;
use Illuminate\Support\Facades\Route;

Route::apiResource('exercise-templates', ExerciseTemplateController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
