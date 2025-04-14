<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;

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
        Date::use(CarbonImmutable::class);
        Http::preventStrayRequests();
        Model::automaticallyEagerLoadRelationships();
        Model::unguard();
        Sleep::fake();
        Vite::useAggressivePrefetching();

        if (app()->isProduction()) {
            DB::prohibitDestructiveCommands();
        } else {
            Model::shouldBeStrict();
        }
    }
}
