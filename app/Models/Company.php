<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
   protected $fillable = [
        'nama_lengkap','nama_toko','alamat_toko','telpon','email','logo_path',
        'license_start_date','license_end_date','license_status','consolidated_report_enabled',
    ];

    protected $casts = [
        'license_start_date' => 'date',
        'license_end_date'   => 'date',
        'consolidated_report_enabled' => 'boolean',
    ];

    public function branches() { return $this->hasMany(Branch::class); }
    public function users()    { return $this->hasMany(User::class); }
}