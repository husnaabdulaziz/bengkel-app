<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use BelongsToCompany, LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'from_branch_id', 'to_branch_id', 'kode_transfer', 'status',
        'requested_by', 'approved_by', 'shipped_by', 'received_by',
        'requested_at', 'approved_at', 'shipped_at', 'received_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function fromBranch() { return $this->belongsTo(Branch::class, 'from_branch_id'); }
    public function toBranch()   { return $this->belongsTo(Branch::class, 'to_branch_id'); }
    public function items()      { return $this->hasMany(StockTransferItem::class); }
}