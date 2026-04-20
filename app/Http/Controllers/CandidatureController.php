<?php
namespace App\Http\Controllers;

use App\Events\CandidatureDeposee;
use App\Events\StatutCandidatureMis;
use App\Models\Candidature;
use App\Models\Offre;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{
    // POST /api/offres/{offre}/candidater
    public function candidater(Request $request, $offre_id)
    {
        $offre = Offre::findOrFail($offre_id);

        if (!$offre->actif) {
            return response()->json(['message' => 'Cette offre n\'est plus active.'], 422);
        }

        $profil = auth('api')->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Vous devez d\'abord créer votre profil.'], 422);
        }

        $dejaPostule = Candidature::where('offre_id', $offre->id)
            ->where('profil_id', $profil->id)
            ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
        ]);

        $candidature = Candidature::create([
            'offre_id'  => $offre->id,
            'profil_id' => $profil->id,
            'message'   => $validated['message'] ?? null,
            'statut'    => 'en_attente',
        ]);

	event(new CandidatureDeposee($candidature));

	return response()->json([
            'message'     => 'Candidature soumise avec succès.',
            'candidature' => $candidature,
        ], 201);
    }

    // GET /api/mes-candidatures
    public function mesCandidatures()
    {
        $profil = auth('api')->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Vous n\'avez pas encore de profil.'], 404);
        }

        $candidatures = Candidature::with(['offre.recruteur'])
            ->where('profil_id', $profil->id)
            ->latest()
            ->get();

        return response()->json($candidatures);
    }

    // GET /api/offres/{offre}/candidatures
    public function candidaturesOffre($offre_id)
    {
        $offre = Offre::findOrFail($offre_id);

        if ($offre->user_id !== auth('api')->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $candidatures = Candidature::with(['profil.user', 'profil.competences'])
            ->where('offre_id', $offre->id)
            ->latest()
            ->get();

        return response()->json($candidatures);
    }

    // PATCH /api/candidatures/{candidature}/statut
    public function changerStatut(Request $request, $candidature_id)
    {
        $candidature = Candidature::with('offre')->findOrFail($candidature_id);

        if ($candidature->offre->user_id !== auth('api')->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
        ]);

        $ancienStatut = $candidature->statut;
        $candidature->update(['statut' => $validated['statut']]);

	event(new StatutCandidatureMis($candidature, $ancienStatut,  $validated['statut']));

        return response()->json([
            'message'     => 'Statut mis à jour.',
            'candidature' => $candidature,
        ]);
    }
}
