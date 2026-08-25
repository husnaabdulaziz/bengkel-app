<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'category_id', 'nama'];

    public function category() { return $this->belongsTo(ProductCategory::class, 'category_id'); }
    public function products() { return $this->hasMany(Product::class, 'subcategory_id'); }
}