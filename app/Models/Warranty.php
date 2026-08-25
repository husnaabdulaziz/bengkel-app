<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id', 'branch_id', 'work_order_item_id', 'product_id', 'customer_id',
        'kode_garansi', 'warranty_start_date', 'warranty_end_date', 'duration_days', 'status',
    ];

    protected function casts(): array
    {
        return [
            'warranty_start_date' => 'date',
            'warranty_end_date' => 'date',
        ];
    }

    public function branch() { return $this->belongsTo(Branch::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function workOrderItem() { return $this->belongsTo(WorkOrderItem::class); }
    public function claims() { return $this->hasMany(WarrantyClaim::class); }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'claimed') return 'claimed';
        if ($this->warranty_end_date->isPast()) return 'expired';
        return 'active';
    }
}