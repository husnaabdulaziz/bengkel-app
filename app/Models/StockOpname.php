<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id', 'branch_id', 'kode_opname', 'opname_date',
        'category_id', 'brand_id', 'status', 'is_adjusted', 'notes', 'created_by', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
            'is_adjusted' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function branch()   { return $this->belongsTo(Branch::class); }
    public function category() { return $this->belongsTo(ProductCategory::class); }
    public function brand()    { return $this->belongsTo(ProductBrand::class); }
    public function items()    { return $this->hasMany(StockOpnameItem::class); }
}