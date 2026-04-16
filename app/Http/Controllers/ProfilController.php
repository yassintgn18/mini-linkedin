<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profil;

class ProfilController extends Controller
{
    //Creation d'un profil utilisateur
    public function store(Request $request){
        $user = auth('api')->user();

        //Verification de l'existence du profil
        if ($user->profil) {
            return response()->json([
                'message' => 'Vous avez déjà un profil'
            ],400);
        }

        //Validation des donnees du profil
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'disponible' => 'nullable|boolean',
        ]);

        //Création des données du profil
        $profil = Profil::create([
            ...$validated,
            'user_id' => $user->id,
        ]);
        return response()->json($profil,201);
    }

    //Affichage du profil de l'utilisateur connecté
    public function show(Request $request){
        $user = auth('api')->user();
        $profil = $user->profil;

        if(!$profil){
            return response()->json([
                'message' => 'Profil inexistant'
            ],404);
        }

        return response()->json($profil);
    }

    //Modification du profil utilisateur
    public function update(Request $request){
        $user = auth('api')->user();
        $profil = $user->profil;

        if(!$profil){
            return response()->json([
                'message' => 'Profil inexistant'
            ],404);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'localisation' => 'sometimes|nullable|string|max:255',
            'disponible' => 'sometimes|nullable|boolean',
        ]);

        $profil->update($validated);
        return response()->json($profil,200);
    }

    //Ajouter une compétence au profil
    public function addCompetence(Request $request){
        $user = auth('api')->user();
        $profil = $user->profil;

        if(!$profil){
            return response()->json([
                'message' => 'Profil inexistant'
            ],404);
        }

        //Validation de la compétence et du niveau
        $validated = $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'niveau' => 'required|in:débutant,intermédiaire,expert'
        ]);

        if($profil->competences->contains($validated['competence_id'])){
            return response()->json([
                'message' => 'Competence est déjà dans votre profil'
            ],400);
        }

        //Ajout dans la table pivot
        $profil->competences()->attach($validated['competence_id'],['niveau'=>$validated['niveau']]);
        return response()->json([
            'message' => 'compétence ajoutée avec succès'
        ],201);
    }

    //Supprimer une compétence du profil
    public function removeCompetence(Request $request, $competence){
        $user = auth('api')->user();
        $profil = $user->profil;

        if(!$profil){
            return response()->json([
                'message' => 'Profil inexistant'
            ],404);
        }

        if(!$profil->competences->contains($competence)){
        return response()->json([
            'message' => 'La compétence n\'existe pas'
        ],404);}

        $profil->competences()->detach($competence);
        return response()->json([
            'message' => 'Compétence supprimée avec succès'
        ],200);
    }

}
