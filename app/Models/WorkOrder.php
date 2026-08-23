<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'invoice_number', 'customer_id', 'stage',
        'queue_number', 'queue_date', 'customer_price_tier',
        'subtotal', 'discount_type', 'discount_value', 'total_amount',
        'payment_method', 'payment_status', 'paid_at', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'paid_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function branch()   { return $this->belongsTo(Branch::class); }
    public function items()    { return $this->hasMany(WorkOrderItem::class); }

    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $discount = $this->discount_type === 'percent'
            ? $subtotal * ($this->discount_value / 100)
            : $this->discount_value;

        $this->update([
            'subtotal' => $subtotal,
            'total_amount' => max($subtotal - $discount, 0),
        ]);
    }
}