<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosingDenomination extends Model
{
    protected $fillable = ['cash_closing_id', 'denomination', 'count', 'reserved_for_next_day'];

    public function cashClosing() { return $this->belongsTo(CashClosing::class); }

    public const DENOMINATIONS = [1000, 2000, 5000, 10000, 20000, 50000, 100000];

    /** Lembar yang masuk ke kamar (brankas) = total dihitung - yang disisihkan untuk besok */
    public function getKamarCountAttribute(): int
    {
        return max($this->count - $this->reserved_for_next_day, 0);
    }

    public function getKamarSubtotalAttribute(): int
    {
        return $this->kamar_count * $this->denomination;
    }

    public function getReservedSubtotalAttribute(): int
    {
        return $this->reserved_for_next_day * $this->denomination;
    }
}