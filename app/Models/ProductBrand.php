<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'nama'];

    public function products() { return $this->hasMany(Product::class, 'brand_id'); }
}