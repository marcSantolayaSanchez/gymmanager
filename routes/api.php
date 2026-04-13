<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MembershipController;

/*
|--------------------------------------------------------------------------
| GymManager – API Routes
|--------------------------------------------------------------------------
*/

// ── Public routes ─────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',  [AuthController::class, 'login']);
});

// ── Protected routes (Sanctum) ────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Dashboard (admin only)
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Resources
    Route::apiResource('clients',     ClientController::class);
    Route::apiResource('trainers',    TrainerController::class);
    Route::apiResource('workouts',    WorkoutController::class);
    Route::apiResource('memberships', MembershipController::class);

    // Extra: Assign membership to client
    Route::post('clients/{client}/assign-membership', [ClientController::class, 'assignMembership']);

    // Extra: Trainer's clients list
    Route::get('trainers/{trainer}/clients', [TrainerController::class, 'clients']);
});
