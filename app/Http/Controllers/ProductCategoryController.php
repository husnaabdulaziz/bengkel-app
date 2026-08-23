<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('nama')->paginate(20);
        return view('master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        ProductCategory::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $productCategory->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}