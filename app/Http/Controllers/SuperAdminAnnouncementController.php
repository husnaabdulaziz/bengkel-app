<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class SuperAdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->get();
        return view('super-admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('super-admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string',
            'target_role' => 'required|in:all,admin_toko,karyawan_toko,teknisi',
        ]);

        $validated['is_active'] = true;
        $validated['created_by'] = auth()->id();

        Announcement::create($validated);

        return redirect()->route('super-admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        return view('super-admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string',
            'target_role' => 'required|in:all,admin_toko,karyawan_toko,teknisi',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = !empty($validated['is_active']);

        $announcement->update($validated);

        return redirect()->route('super-admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}