<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;

// Public routes (no authentication needed)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (JWT token required)
Route::middleware(['auth:api','role:candidat'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});


// Test route for role middleware (temporary, remove later)
Route::middleware(['auth:api', 'role:admin'])->get('/admin/test', function () {
    return response()->json(['message' => 'Welcome admin!']);
});

//Routes pour la connexion au profil du candidat
Route::middleware('role:candidat')->group(function () {
    Route::post('/profil', [ProfilController::class, 'store']);
    Route::get('/profil', [ProfilController::class, 'show']);
    Route::put('/profil', [ProfilController::class, 'update']);
    Route::post('/profil/competences', [ProfilController::class, 'addCompetence']);
    Route::delete('/profil/competences/{competence}', [ProfilController::class, 'removeCompetence']);
});

