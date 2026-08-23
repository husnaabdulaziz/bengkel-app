<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
    'work_order_id', 'product_id', 'item_name', 'price_tier_used',
    'unit_price', 'quantity', 'subtotal', 'is_warranty_claim', 'warranty_id', 'manual_fee',
        ];

    protected function casts(): array
        {
            return [
                'unit_price' => 'decimal:2',
                'subtotal' => 'decimal:2',
                'is_warranty_claim' => 'boolean',
                'manual_fee' => 'boolean',
            ];
        }

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function product()  { return $this->belongsTo(Product::class); }
    public function technicians() { return $this->hasMany(WorkOrderItemTechnician::class); }
}