<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'branch_id', 'user_id', 'action',
        'model_type', 'model_id', 'description', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    protected static function booted()
    {
        static::creating(function ($log) {
            $log->created_at = $log->created_at ?? now();
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function company() { return $this->belongsTo(Company::class); }
    
}