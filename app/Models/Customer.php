<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToCompany, SoftDeletes, LogsActivity;

    protected $fillable = [
        'company_id', 'branch_id', 'nama', 'telpon', 'plat_nomor',
        'alamat', 'jenis_kendaraan', 'merk_kendaraan', 'model_kendaraan', 'last_visit_at',
    ];

    protected function casts(): array
    {
        return ['last_visit_at' => 'datetime'];
    }

    public function branch() { return $this->belongsTo(Branch::class); }
    public function workOrders() { return $this->hasMany(WorkOrder::class); }

    /** Filter pelanggan yang belum kembali dalam sekian bulan */
    public function scopeNotVisitedSince($query, int $months)
    {
        return $query->where(function ($q) use ($months) {
            $q->whereNull('last_visit_at')
              ->orWhere('last_visit_at', '<=', now()->subMonths($months));
        });
    }
}