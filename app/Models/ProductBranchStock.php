<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBranchStock extends Model
{
    const CREATED_AT = null;

    protected $fillable = ['product_id', 'branch_id', 'stock_qty', 'minimum_stock_override'];

    public function product() { return $this->belongsTo(Product::class); }
    public function branch()  { return $this->belongsTo(Branch::class); }
}