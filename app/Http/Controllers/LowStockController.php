<?php

namespace App\Http\Controllers;

use App\Models\ProductBranchStock;

class LowStockController extends Controller
{
    public function index()
    {
        $lowStockItems = $this->getLowStockItems();

        return view('master.products.low-stock', compact('lowStockItems'));
    }

    /** Dipakai juga oleh navbar untuk hitung badge notifikasi */
    public static function getLowStockItems()
    {
        return ProductBranchStock::with('product.category', 'product.brand', 'branch')
            ->whereHas('product', function ($q) {
                $q->where('is_jasa', false)->where('minimum_stock', '>', 0);
            })
            ->get()
            ->filter(function ($stock) {
                return $stock->product && $stock->stock_qty <= $stock->product->minimum_stock;
            })
            ->sortBy('stock_qty')
            ->values();
    }
}