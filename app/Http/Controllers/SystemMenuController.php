<?php

namespace App\Http\Controllers;

use App\Models\SystemMenuSetting;
use App\Support\MenuPermissions;
use Illuminate\Http\Request;

class SystemMenuController extends Controller
{
    public function edit()
    {
        $current = SystemMenuSetting::pluck('enabled', 'menu_key');

        return view('super-admin.system-menus', [
            'menuList' => MenuPermissions::LIST,
            'current' => $current,
        ]);
    }

    public function update(Request $request)
    {
        $enabledKeys = $request->input('menus', []);

        foreach (array_keys(MenuPermissions::LIST) as $key) {
            SystemMenuSetting::updateOrCreate(
                ['menu_key' => $key],
                ['enabled' => in_array($key, $enabledKeys)]
            );
        }

        return back()->with('success', 'Pengaturan menu sistem berhasil disimpan.');
    }
}