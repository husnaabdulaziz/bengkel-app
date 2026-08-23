<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'nama'];

    public function products() { return $this->hasMany(Product::class, 'category_id'); }
}