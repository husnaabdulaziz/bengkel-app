<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TechnicianManualFee extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'user_id', 'product_id', 'transaction_date', 'fee_amount', 'notes', 'created_by'];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'fee_amount' => 'decimal:2',
        ];
    }

    public function technician() { return $this->belongsTo(User::class, 'user_id'); }
    public function product()    { return $this->belongsTo(Product::class); }
}