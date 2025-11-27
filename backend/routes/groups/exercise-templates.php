<?php

use App\Domain\ExerciseTemplate\Controllers\ExerciseTemplateController;
use App\Domain\ExerciseTemplate\Controllers\ExerciseTemplateParameterController;
use Illuminate\Support\Facades\Route;

Route::apiResource('exercise-templates', ExerciseTemplateController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::prefix('exercise-templates/{uuid}/parameters')->group(function () {
    Route::get('/', [ExerciseTemplateParameterController::class, 'index'])
        ->name('exercise-templates.parameters.index');
    Route::post('/', [ExerciseTemplateParameterController::class, 'store'])
        ->name('exercise-templates.parameters.store');
});

Route::prefix('parameters')->group(function () {
    Route::put('{parameterUuid}', [ExerciseTemplateParameterController::class, 'update'])
        ->name('exercise-templates.parameters.update');
    Route::delete('{parameterUuid}', [ExerciseTemplateParameterController::class, 'destroy'])
        ->name('exercise-templates.parameters.destroy');
});

Route::get('exercise-templates/{uuid}/steps', [ExerciseTemplateController::class, 'steps'])
    ->name('exercise-templates.steps');
