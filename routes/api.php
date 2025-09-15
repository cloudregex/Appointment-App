<?php

use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/tenant/login', [TenantAuthController::class, 'login']);

Route::middleware(['tenant'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
