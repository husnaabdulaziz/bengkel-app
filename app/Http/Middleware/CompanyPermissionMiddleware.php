<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

class CompanyPermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();

        // Super Admin yang sedang "Bertindak Sebagai Toko" dianggap punya semua izin
        // (setara Admin Toko) tanpa perlu dicek satu-satu ke tabel permission.
        if ($user && $user->is_super_admin) {
            return $next($request);
        }

        return app(PermissionMiddleware::class)->handle($request, $next, implode('|', $permissions));
    }
}