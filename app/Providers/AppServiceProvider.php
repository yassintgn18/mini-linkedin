<?php

namespace App\Providers;

use App\Events\CandidatureDeposee;
use App\Listeners\LogCandidatureDeposee;
use App\Events\StatutCandidatureMis;
use App\Listeners\LogStatutCandidatureMis;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            CandidatureDeposee::class,
            LogCandidatureDeposee::class,
        );

        Event::listen(
            StatutCandidatureMis::class,
            LogStatutCandidatureMis::class,
        );

        Schema::defaultStringLength(191);
    }
}
