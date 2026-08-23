<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->orderBy('nama')->paginate(20)->withQueryString();
        $categories = ProductCategory::orderBy('nama')->get();
        $brands = ProductBrand::orderBy('nama')->get();

        return view('master.products.index', compact('products', 'categories', 'brands'));
    }

    /** JSON: data list produk untuk live filter (Alpine.js) */
    public function data(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $perPage = in_array((int) $request->get('per_page'), [10, 25, 50, 100]) ? (int) $request->get('per_page') : 10;
$products = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        $items = $products->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'is_jasa' => $p->is_jasa,
                'category' => $p->category?->nama,
                'brand' => $p->brand?->nama,
                'harga_jual' => number_format($p->harga_jual, 0, ',', '.'),
                'harga_jual_jasa' => number_format($p->harga_jual_jasa, 0, ',', '.'),
                'harga_online' => number_format($p->harga_online, 0, ',', '.'),
                'harga_ojol' => number_format($p->harga_ojol, 0, ',', '.'),
                'edit_url' => route('products.edit', $p),
                'delete_url' => route('products.destroy', $p),
            ];
        });

        return response()->json([
            'items' => $items,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total(),
        ]);
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('nama')->get();
        $brands = ProductBrand::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('master.products.create', compact('categories', 'brands', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('nama')->get();
        $brands = ProductBrand::orderBy('nama')->get();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('master.products.edit', compact('product', 'categories', 'brands', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'default_supplier_id' => 'nullable|exists:suppliers,id',
            'sku' => 'nullable|string|max:60',
            'nama' => 'required|string|max:180',
            'satuan' => 'required|string|max:30',
            'is_jasa' => 'boolean',
            'harga_modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'harga_jual_jasa' => 'required|numeric|min:0',
            'harga_online' => 'required|numeric|min:0',
            'harga_ojol' => 'required|numeric|min:0',
            'garansi_aktif' => 'boolean',
            'garansi_durasi_hari' => 'nullable|integer|min:1',
            'minimum_stock' => 'required|integer|min:0',
        ]);
    }
}