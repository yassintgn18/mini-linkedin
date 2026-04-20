<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\CandidatureDeposee;


class LogCandidatureDeposee
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   public function handle(CandidatureDeposee $event): void
{
    $candidature = $event->candidature;

    $profil = $candidature->profil;
    $user = $profil ? $profil->user : null;
    $offre = $candidature->offre;

    Log::channel('candidatures')->info(
        "[" . now() . "] Candidature déposée par " . $user->name . " à l'offre " . $offre->titre
    );
}
}
