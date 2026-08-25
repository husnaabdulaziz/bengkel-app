<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::with('categories')->orderBy('nama')->paginate(20);
        $categories = ProductCategory::orderBy('nama')->get();
        return view('master.brands.index', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        $brand = ProductBrand::create(['nama' => $validated['nama']]);
        $brand->categories()->sync($validated['category_ids']);

        return back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, ProductBrand $productBrand)
    {
        $validated = $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        $productBrand->update(['nama' => $validated['nama']]);
        $productBrand->categories()->sync($validated['category_ids']);

        return back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(ProductBrand $productBrand)
    {
        $productBrand->delete();
        return back()->with('success', 'Brand berhasil dihapus.');
    }
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'nama' => 'required|string|max:100',
        ]);

        $brand = ProductBrand::create(['nama' => $validated['nama']]);
        $brand->categories()->attach($validated['category_id']);

        return response()->json(['id' => $brand->id, 'nama' => $brand->nama, 'category_ids' => [(int) $validated['category_id']]]);
    }
}