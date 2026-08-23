<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['company_id','nama_cabang','alamat','telpon','is_main','status'];

    public function company() { return $this->belongsTo(Company::class); }
    public function users()   { return $this->belongsToMany(User::class, 'user_branches'); }
}