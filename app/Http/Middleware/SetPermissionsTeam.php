<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\PermissionRegistrar;

class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->company_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);
        }
        return $next($request);
    }
}