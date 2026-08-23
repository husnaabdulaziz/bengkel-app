<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['stock_transfer_id', 'product_id', 'qty_requested', 'qty_approved', 'qty_shipped', 'qty_received'];

    public function transfer() { return $this->belongsTo(StockTransfer::class); }
    public function product()  { return $this->belongsTo(Product::class); }
}