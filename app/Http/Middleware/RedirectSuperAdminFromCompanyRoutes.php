<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSuperAdminFromCompanyRoutes
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_super_admin) {
            $allowedPatterns = ['super-admin/*', 'profile', 'profile/*', 'logout'];

            $isAllowed = false;
            foreach ($allowedPatterns as $pattern) {
                if ($request->is($pattern)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->route('super-admin.users.index')
                    ->with('error', 'Akun Super Admin tidak terikat ke toko manapun. Gunakan menu "Kelola User" untuk kelola user per toko, atau login sebagai user toko tertentu untuk akses fitur operasional.');
            }
        }

        return $next($request);
    }
}