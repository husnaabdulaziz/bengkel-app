<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductFee extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'product_id', 'fee_type', 'fee_value'];

    protected function casts(): array
    {
        return ['fee_value' => 'decimal:2'];
    }

    public function product() { return $this->belongsTo(Product::class); }

    /** Hitung nominal fee untuk 1 unit berdasarkan subtotal item */
    public function calculateFee(float $subtotal, int $quantity): float
    {
        return $this->fee_type === 'percent'
            ? $subtotal * ((float) $this->fee_value / 100)
            : (float) $this->fee_value * $quantity;
    }
}