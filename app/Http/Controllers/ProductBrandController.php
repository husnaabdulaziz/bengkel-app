<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::orderBy('nama')->paginate(20);
        return view('master.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        ProductBrand::create($validated);

        return back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, ProductBrand $productBrand)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $productBrand->update($validated);

        return back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(ProductBrand $productBrand)
    {
        $productBrand->delete();
        return back()->with('success', 'Brand berhasil dihapus.');
    }
}