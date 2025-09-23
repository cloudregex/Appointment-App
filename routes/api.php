<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::post('/tenant/login', [TenantAuthController::class, 'login']);

Route::middleware(['tenant'])->group(function () {

    // Patient routes
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::put('/patients/{id}', [PatientController::class, 'update']);
    Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

    // Patient routes
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);

    // Doctors List get
    Route::get('/doctors-list', [PatientController::class, 'doctorsList']);
    Route::get('/patients-list', [PatientController::class, 'PatientList']);
});
