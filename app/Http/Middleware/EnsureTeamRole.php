<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que l'utilisateur authentifié possède l'un des rôles autorisés
 * au sein de l'équipe ciblée par la route (paramètre {team}).
 *
 * Exemple : Route::middleware('team.role:admin,manager')
 */
class EnsureTeamRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $team = $request->route('team');

        if (! $team) {
            abort(404);
        }

        $userRole = $request->user()->roleInTeam($team);

        if (! $userRole || ! in_array($userRole, $roles, true)) {
            abort(403, "Rôle insuffisant pour cette action sur l'équipe.");
        }

        return $next($request);
    }
}
