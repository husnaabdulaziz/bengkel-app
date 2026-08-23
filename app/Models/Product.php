<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'category_id', 'brand_id', 'default_supplier_id',
        'sku', 'nama', 'satuan', 'is_jasa',
        'harga_modal', 'harga_jual', 'harga_jual_jasa', 'harga_online', 'harga_ojol',
        'garansi_aktif', 'garansi_durasi_hari', 'minimum_stock', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_jasa' => 'boolean',
            'garansi_aktif' => 'boolean',
            'harga_modal' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'harga_jual_jasa' => 'decimal:2',
            'harga_online' => 'decimal:2',
            'harga_ojol' => 'decimal:2',
        ];
    }

    public function category() { return $this->belongsTo(ProductCategory::class, 'category_id'); }
    public function brand()    { return $this->belongsTo(ProductBrand::class, 'brand_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class, 'default_supplier_id'); }
    public function branchStocks() { return $this->hasMany(ProductBranchStock::class); }

    /** Ambil harga sesuai tarif yang dipilih di POS nanti */
    public function getPriceForTier(string $tier): float
    {
        return match ($tier) {
            'harga_jual_jasa' => (float) $this->harga_jual_jasa,
            'harga_online'    => (float) $this->harga_online,
            'harga_ojol'      => (float) $this->harga_ojol,
            default           => (float) $this->harga_jual,
        };
    }

    /** Stock di cabang tertentu (dipanggil nanti di POS/Inventory) */
    public function stockAtBranch(int $branchId): int
    {
        return $this->branchStocks()->where('branch_id', $branchId)->value('stock_qty') ?? 0;
    }
}