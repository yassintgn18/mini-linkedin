<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offre;

class OffreController extends Controller
{
    public function index(Request $request)
    {
        $query = Offre::query();
        $query->where('actif', true);
        if ($request->localisation) {
            $query->where('localisation', $request->localisation);
        }
        if($request->type){
            $query->where('type', $request->type);
        }

        $offres = $query->latest()->paginate(10);
        return response()->json($offres);
    }

    public function show($offre_id){
        $offre = Offre::findorFail($offre_id);
        return response()->json($offre);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'titre' => 'required|string',
            'description'=> 'required|string',
            'localisation'=> 'nullable|string',
            'type'=> 'required|in:CDI,CDD,stage',
            'actif'=> 'boolean',
        ]);

        Offre::create([
            ...$validated,'user_id' => auth('api')->user()->id
        ]);
        return response()->json([
            'message' => 'Offre créée avec succès'
        ],201);
    }

    public function update(Request $request, $offre_id){

        $offre = Offre::findorFail($offre_id);

        if($offre->user_id != auth('api')->user()->id){
            return response()->json([
                'message' => 'Accès refusé'
            ],403);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|string',
            'description' => 'sometimes|string',
            'localisation' => 'sometimes|string',
            'type' => 'sometimes|in:CDI,CDD,stage',
            'actif' => 'sometimes|boolean',
        ]);

        $offre->update($validated);
        return response()->json([
            'message' => 'Mise à jour réussie',
            'offre' => $offre
        ]);

    }

    public function destroy($offre_id){

        $offre = Offre::findorFail($offre_id);

        if($offre->user_id != auth('api')->user()->id){
            return response()->json([
                'message' => 'Accès refusé'
            ],403);
        }

        $offre->delete();
        return response()->json([
            'message' => 'Offre supprimée avec succès'
        ]);
    }
}
