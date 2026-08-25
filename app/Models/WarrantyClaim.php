<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    public $timestamps = false;

    protected $fillable = ['warranty_id', 'claim_date', 'work_order_id', 'notes', 'created_by'];

    protected function casts(): array
    {
        return ['claim_date' => 'date', 'created_at' => 'datetime'];
    }

    public function warranty() { return $this->belongsTo(Warranty::class); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}