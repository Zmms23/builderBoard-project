<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        setPermissionsTeamId($tenant?->getKey());

        if ($user = $request->user()) {
            $user->unsetRelation('roles');
        }

        return $next($request);
    }
}