<?php

use App\Domain\ExerciseTemplate\Controllers\ExerciseTemplateController;
use Illuminate\Support\Facades\Route;

Route::get('exercise-templates/all-list', [ExerciseTemplateController::class, 'allList']);

Route::apiResource('exercise-templates', ExerciseTemplateController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

