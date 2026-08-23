<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderItemTechnician extends Model
{
    public $timestamps = false;

    protected $fillable = ['work_order_item_id', 'user_id', 'fee_amount', 'fee_notes'];

    public function item()      { return $this->belongsTo(WorkOrderItem::class, 'work_order_item_id'); }
    public function technician(){ return $this->belongsTo(User::class, 'user_id'); }
}