<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = Filament::getTenant()?->getKey();
        $user = $request->user();

        if ($tenantId === null) {
            $routeTenant = $request->route('tenant');

            if ($routeTenant instanceof Model) {
                $tenantId = $routeTenant->getKey();
            } elseif (is_numeric($routeTenant)) {
                $tenantId = (int) $routeTenant;
            }
        }

        if (($tenantId === null) && is_object($user) && method_exists($user, 'companies')) {
            $companyIds = $user->companies()
                ->orderBy('name')
                ->pluck('companies.id');

            $originalTeamId = getPermissionsTeamId();

            foreach ($companyIds as $companyId) {
                setPermissionsTeamId($companyId);
                $user->unsetRelation('roles');

                if ($user->can('Create:Company')) {
                    $tenantId = $companyId;

                    break;
                }
            }

            setPermissionsTeamId($originalTeamId);
            $user->unsetRelation('roles');

            $tenantId ??= $companyIds->first();
        }

        setPermissionsTeamId($tenantId);

        if ($user) {
            $user->unsetRelation('roles');
        }

        return $next($request);
    }
}
