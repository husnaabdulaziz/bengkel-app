<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminUserController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::orderBy('nama_toko')->get();
        $companyId = $request->get('company_id', $companies->first()?->id);

        $users = User::where('company_id', $companyId)
            ->with('branches')
            ->orderBy('name')
            ->get();

        if ($companyId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($companyId);
            foreach ($users as $user) {
                $user->currentRole = $user->roles->first()?->name;
            }
        }

        return view('super-admin.users.index', compact('companies', 'companyId', 'users'));
    }

    public function create(Request $request)
    {
        $companies = Company::orderBy('nama_toko')->get();
        $companyId = $request->get('company_id', $companies->first()?->id);
        $branches = Branch::where('company_id', $companyId)->get();

        return view('super-admin.users.create', compact('companies', 'companyId', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:150',
            'username' => 'nullable|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin_toko,karyawan_toko,teknisi',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'company_id' => $validated['company_id'],
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'is_super_admin' => false,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['branch_ids'])) {
            $user->branches()->attach($validated['branch_ids']);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($validated['company_id']);
        $user->assignRole($validated['role']);

        return redirect()->route('super-admin.users.index', ['company_id' => $validated['company_id']])
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $companies = Company::orderBy('nama_toko')->get();
        $branches = Branch::where('company_id', $user->company_id)->get();

        app(PermissionRegistrar::class)->setPermissionsTeamId($user->company_id);
        $currentRole = $user->roles->first()?->name;
        $currentBranchIds = $user->branches->pluck('id')->toArray();

        return view('super-admin.users.edit', compact('user', 'companies', 'branches', 'currentRole', 'currentBranchIds'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin_toko,karyawan_toko,teknisi',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'status' => $validated['status'],
            'password' => !empty($validated['password']) ? bcrypt($validated['password']) : $user->password,
        ]);

        $user->branches()->sync($validated['branch_ids'] ?? []);

        app(PermissionRegistrar::class)->setPermissionsTeamId($user->company_id);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('super-admin.users.index', ['company_id' => $user->company_id])
            ->with('success', 'User berhasil diperbarui.');
    }
}