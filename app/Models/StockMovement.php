<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use BelongsToCompany;

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'branch_id', 'product_id', 'type', 'quantity',
        'reference_type', 'reference_id', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    protected static function booted()
    {
        static::creating(function ($movement) {
            $movement->created_at = $movement->created_at ?? now();
            $movement->created_by = $movement->created_by ?? auth()->id();
        });
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function branch()  { return $this->belongsTo(Branch::class); }

    /** Arah pergerakan: true jika menambah stock, false jika mengurangi */
    public function isIncoming(): bool
    {
        return in_array($this->type, ['in', 'adjustment_in', 'transfer_in']);
    }
}