<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductStickerController extends Controller
{
    public function index(Request $request)
    {
        $brands = ProductBrand::orderBy('nama')->get();
        $categories = ProductCategory::orderBy('nama')->get();
        $selectedBrandId = $request->get('brand_id');
        $selectedCategoryId = $request->get('category_id');

        $products = collect();
        if ($selectedBrandId || $selectedCategoryId) {
            $query = Product::where('status', 'active');

            if ($selectedBrandId) {
                $query->where('brand_id', $selectedBrandId);
            }
            if ($selectedCategoryId) {
                $query->where('category_id', $selectedCategoryId);
            }

            $products = $query->orderBy('model_name')->orderBy('ukuran')->get();
        }

        $groups = $products->groupBy(function ($p) {
            return $p->model_name ?: 'Produk: ' . $p->nama;
        });

        return view('master.products.stickers', compact('brands', 'categories', 'selectedBrandId', 'selectedCategoryId', 'groups'));
    }
}