<?php

namespace App\Http\Middleware;

use App\Models\SystemMenuSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuEnabled
{
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        // Super Admin selalu bisa lewat, meski menu sedang dinonaktifkan (supaya bisa dinyalakan lagi)
        if (auth()->check() && auth()->user()->is_super_admin) {
            return $next($request);
        }

        if (!SystemMenuSetting::isEnabled($menuKey)) {
            abort(403, 'Menu ini sedang dinonaktifkan oleh Super Admin.');
        }

        return $next($request);
    }
}