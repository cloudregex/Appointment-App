<?php

use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::post('/tenant/login', [TenantAuthController::class, 'login']);

Route::middleware(['tenant'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    // Patient routes
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::put('/patients/{id}', [PatientController::class, 'update']);
    Route::delete('/patients/{id}', [PatientController::class, 'destroy']);
});
