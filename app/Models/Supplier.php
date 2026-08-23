<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'nama', 'contact_person', 'telpon', 'alamat'];
}