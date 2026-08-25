<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id', 'branch_id', 'supplier_id', 'invoice_number',
        'purchase_date', 'total_amount', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['purchase_date' => 'date'];
    }

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function branch()   { return $this->belongsTo(Branch::class); }
    public function items()    { return $this->hasMany(PurchaseItem::class); }
}