<?php
namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\User;

class AdminController extends Controller
{
    // GET /api/admin/users
    public function listUsers()
    {
        return response()->json(User::with('profil')->latest()->get());
    }

    // DELETE /api/admin/users/{user}
    public function deleteUser($user_id)
    {
        $user = User::findOrFail($user_id);

        if ($user->id === auth('api')->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        $user->delete();
        return response()->json(['message' => 'Compte supprimé avec succès.']);
    }

    // PATCH /api/admin/offres/{offre}
    public function toggleOffre($offre_id)
    {
        $offre = Offre::findOrFail($offre_id);
        $offre->update(['actif' => !$offre->actif]);

        $etat = $offre->actif ? 'activée' : 'désactivée';
        return response()->json([
            'message' => "Offre {$etat} avec succès.",
            'offre'   => $offre,
        ]);
    }
}
