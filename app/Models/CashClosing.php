<?php
namespace App\Models;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
class CashClosing extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id', 'branch_id', 'closing_date', 'opening_balance',
        'cash_sales', 'cash_expenses', 'expected_balance', 'actual_balance',
        'difference', 'notes', 'status', 'opened_by', 'closed_by', 'closed_at',
    ];
    protected function casts(): array
    {
        return [
            'closing_date' => 'date',
            'closed_at' => 'datetime',
            'opening_balance' => 'decimal:2',
            'cash_sales' => 'decimal:2',
            'cash_expenses' => 'decimal:2',
            'expected_balance' => 'decimal:2',
            'actual_balance' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function openedBy() { return $this->belongsTo(User::class, 'opened_by'); }
    public function closedBy() { return $this->belongsTo(User::class, 'closed_by'); }
    public function denominations() { return $this->hasMany(CashClosingDenomination::class); }
}