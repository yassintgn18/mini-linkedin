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
        $user = $event->user;
        $offre = $event->offre;

        Log::info("Candidature Déposée par " . $user->name." à l'offre ".$offre->titre);
    }
}
