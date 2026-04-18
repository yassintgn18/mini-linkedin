<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OffreController;
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

//Routes liées aux offres
Route::middleware('auth:api')->group(function () {
    Route::get('offres', [OffreController::class, 'index']);
    Route::get('offres/{offre}', [OffreController::class, 'show']);
});

//Uniquement pour les recruteurs
Route::middleware(['auth:api', 'role:recruteur'])->group(function () {
    Route::post('/offres', [OffreController::class, 'store']);
    Route::put('/offres/{offre}', [OffreController::class, 'update']);
    Route::delete('/offres/{offre}', [OffreController::class, 'destroy']);
});

// Candidatures — candidats
Route::middleware(['auth:api', 'role:candidat'])->group(function () {
    Route::post('/offres/{offre}/candidater', [CandidatureController::class, 'candidater']);
    Route::get('/mes-candidatures', [CandidatureController::class, 'mesCandidatures']);
});

// Candidatures — recruteurs
Route::middleware(['auth:api', 'role:recruteur'])->group(function () {
    Route::get('/offres/{offre}/candidatures', [CandidatureController::class, 'candidaturesOffre']);
    Route::patch('/candidatures/{candidature}/statut', [CandidatureController::class, 'changerStatut']);
});

// Administration
Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
    Route::patch('/offres/{offre}', [AdminController::class, 'toggleOffre']);
});
