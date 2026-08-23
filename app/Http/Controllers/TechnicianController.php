<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = User::role('teknisi')->orderBy('name')->paginate(20);
        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'inisial' => 'required|string|max:10',
            'telpon' => 'nullable|string|max:30',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'company_id' => auth()->user()->company_id,
            'name' => $validated['name'],
            'inisial' => strtoupper($validated['inisial']),
            'phone' => $validated['telpon'] ?? null,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => 'active',
        ]);

        $user->assignRole('teknisi');

        return redirect()->route('technicians.index')->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function edit(User $technician)
    {
        return view('technicians.edit', ['technician' => $technician]);
    }

    public function update(Request $request, User $technician)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'inisial' => 'required|string|max:10',
            'telpon' => 'nullable|string|max:30',
            'email' => 'required|email|max:150|unique:users,email,' . $technician->id,
            'password' => 'nullable|string|min:6',
        ]);

        $technician->update([
            'name' => $validated['name'],
            'inisial' => strtoupper($validated['inisial']),
            'phone' => $validated['telpon'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $technician->password,
        ]);

        return redirect()->route('technicians.index')->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy(User $technician)
    {
        $technician->delete();
        return back()->with('success', 'Teknisi berhasil dihapus.');
    }
}