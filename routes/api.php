<?php

use App\Http\Controllers\Api\PatientApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->group(function () {
    Route::post('login', [PatientApiController::class, 'login']);

    Route::middleware('patient.api')->group(function () {
        Route::get('appointments', [PatientApiController::class, 'appointments']);
        Route::get('lab-results', [PatientApiController::class, 'labResults']);
        Route::get('ray-results', [PatientApiController::class, 'rayResults']);
        Route::get('prescriptions', [PatientApiController::class, 'prescriptions']);
        Route::get('follow-ups', [PatientApiController::class, 'followUps']);
        Route::get('queue/{ticketNumber}', [PatientApiController::class, 'queuePosition']);
        Route::post('ambulance', [PatientApiController::class, 'requestAmbulance']);
        Route::get('ambulance/{id}', [PatientApiController::class, 'ambulanceStatus']);
    });
});
