<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\Models\ProductBranchStock;

class StockMovementObserver
{
    public function created(StockMovement $movement): void
    {
        $stock = ProductBranchStock::firstOrCreate(
            ['product_id' => $movement->product_id, 'branch_id' => $movement->branch_id],
            ['stock_qty' => 0]
        );

        $delta = $movement->isIncoming() ? $movement->quantity : -$movement->quantity;

        $stock->increment('stock_qty', $delta);
    }
}