<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActAsCompanyForSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_super_admin) {
            $companyId = session('acting_company_id');

            if ($companyId && $company = Company::find($companyId)) {
                // Timpa company_id di memori (tidak disimpan ke database) supaya seluruh
                // kode yang sudah ada (auth()->user()->company_id / ->company) otomatis
                // memakai konteks toko yang dipilih, tanpa perlu ubah kode di banyak tempat.
                $user->company_id = $company->id;
                $user->setRelation('company', $company);
            } else {
                $allowed = ['super-admin/*', 'profile', 'profile/*', 'logout', 'switch-company', 'switch-company/*'];
                $isAllowed = false;
                foreach ($allowed as $pattern) {
                    if ($request->is($pattern)) {
                        $isAllowed = true;
                        break;
                    }
                }

                if (!$isAllowed) {
                    return redirect()->route('switch-company.index')
                        ->with('error', 'Pilih toko dulu untuk mengakses fitur operasional.');
                }
            }
        }

        return $next($request);
    }
}