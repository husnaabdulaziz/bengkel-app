<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPayment extends Model
{
    protected $fillable = ['work_order_id', 'payment_method', 'amount'];

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}