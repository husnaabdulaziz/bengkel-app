<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['stock_opname_id', 'product_id', 'system_stock', 'real_stock', 'notes'];

    protected $appends = ['difference'];

    public function getDifferenceAttribute(): int
    {
        return $this->real_stock - $this->system_stock;
    }

    public function opname()  { return $this->belongsTo(StockOpname::class); }
    public function product() { return $this->belongsTo(Product::class); }
}