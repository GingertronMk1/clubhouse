<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubUser;
use App\Models\User;
use App\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $clubs = $user->clubs()->orderBy('name')->get(['clubs.id', 'clubs.slug', 'clubs.name'])
            ->map(function (Club $club) use ($user): array {
                $membership = $club->getRelationValue('pivot');

                return [
                    'id' => $club->id,
                    'slug' => $club->slug,
                    'name' => $club->name,
                    'can_view' => $user->superadmin || ($membership instanceof ClubUser && in_array(Permission::View->value, $membership->permissions ?? [], true)),
                ];
            });

        return Inertia::render('profile/Show', [
            'user' => $user->only(['name', 'email']),
            'clubs' => $clubs,
        ]);
    }
}
