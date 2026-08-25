<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'category_id', 'nama'];

    public function products() { return $this->hasMany(Product::class, 'brand_id'); }
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_brand_category', 'product_brand_id', 'product_category_id');
    }
}