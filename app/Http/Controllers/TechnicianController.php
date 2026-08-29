<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        ]);

        // Email & password login dibuat otomatis (tidak ditentukan Admin Toko).
        // Status default "inactive" karena kredensial belum diisi asli — Super Admin
        // yang akan mengaktifkan sekaligus isi email/password lewat menu Kelola User.
        $autoEmail = Str::slug($validated['name']) . '-' . Str::random(6) . '@internal.local';

        $user = User::create([
            'company_id' => auth()->user()->company_id,
            'name' => $validated['name'],
            'inisial' => strtoupper($validated['inisial']),
            'phone' => $validated['telpon'] ?? null,
            'email' => $autoEmail,
            'password' => bcrypt(Str::random(24)),
            'status' => 'inactive',
        ]);

        $user->assignRole('teknisi');

        return redirect()->route('technicians.index')->with('success', 'Teknisi berhasil ditambahkan. Mekanik sudah bisa dipilih di POS. Untuk memberi akses login, atur lewat menu Super Admin > Kelola User.');
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
        ]);

        $technician->update([
            'name' => $validated['name'],
            'inisial' => strtoupper($validated['inisial']),
            'phone' => $validated['telpon'] ?? null,
        ]);

        return redirect()->route('technicians.index')->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy(User $technician)
    {
        $technician->delete();
        return back()->with('success', 'Teknisi berhasil dihapus.');
    }
}