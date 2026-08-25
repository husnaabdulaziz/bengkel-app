<?php

namespace App\Http\Controllers;

use App\Models\ProductSubcategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductSubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = ProductSubcategory::with('category')->orderBy('nama')->paginate(20);
        $categories = ProductCategory::orderBy('nama')->get();
        return view('master.subcategories.index', compact('subcategories', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        ProductSubcategory::create($validated);

        return back()->with('success', 'Sub kategori berhasil ditambahkan.');
    }

    public function update(Request $request, ProductSubcategory $subcategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        $subcategory->update($validated);

        return back()->with('success', 'Sub kategori berhasil diperbarui.');
    }

    public function destroy(ProductSubcategory $subcategory)
    {
        $subcategory->delete();
        return back()->with('success', 'Sub kategori berhasil dihapus.');
    }
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        $sub = ProductSubcategory::create($validated);

        return response()->json(['id' => $sub->id, 'nama' => $sub->nama, 'category_id' => $sub->category_id]);
    }
}