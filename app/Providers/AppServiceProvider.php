<?php

namespace App\Providers;

use App\Models\Club;
use App\Models\Game;
use App\Models\User;
use App\Permission;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();

        Gate::before(fn (User $user): ?bool => $user->superadmin ? true : null);
        Gate::define('manage-sports', fn (User $user): bool => false);
        Gate::define('create-team', fn (User $user, Club $club): bool => $user->hasClubPermission($club, Permission::Create));
        Gate::define('update-team', fn (User $user, Club $club): bool => $user->hasClubPermission($club, Permission::Update));
        Gate::define('delete-team', fn (User $user, Club $club): bool => $user->hasClubPermission($club, Permission::Delete));
        Gate::define('create-game-event', function (User $user, Game $game): bool {
            foreach ([$game->team1?->club, $game->team2?->club] as $club) {
                if ($club && $user->hasClubPermission($club, Permission::Create)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Model::shouldBeStrict();

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
