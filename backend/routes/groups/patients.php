<?php

use App\Domain\Patient\Controllers\PatientController;
use App\Domain\Patient\Controllers\PatientDeviceController;
use App\Domain\Patient\Controllers\PatientExaminationController;
use Illuminate\Support\Facades\Route;

Route::get('patients/search', [PatientController::class, 'search']);


Route::get('patients/{uuid}/examinations', [PatientExaminationController::class, 'index']);
Route::post('patients/{uuid}/examinations', [PatientExaminationController::class, 'store']);
Route::get('patients/{uuid}/devices', [PatientDeviceController::class, 'index']);
Route::post('patients/{uuid}/devices/token', [PatientDeviceController::class, 'generateToken']);
Route::delete('patients/{uuid}/devices/{deviceUuid}', [PatientDeviceController::class, 'destroy']);

Route::apiResource('patients', PatientController::class)
    ->only(['index', 'show', 'store', 'update', 'destroy']);
