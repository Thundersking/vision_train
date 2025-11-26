<?php

use App\Domain\Patient\Controllers\PatientController;
use App\Domain\Patient\Controllers\PatientExaminationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('patients', PatientController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);

Route::get('patients/{uuid}/examinations', [PatientExaminationController::class, 'index']);
Route::post('patients/{uuid}/examinations', [PatientExaminationController::class, 'store']);
