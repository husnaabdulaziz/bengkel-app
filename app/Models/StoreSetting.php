<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'branch_id', 'setting_key', 'setting_value'];
}