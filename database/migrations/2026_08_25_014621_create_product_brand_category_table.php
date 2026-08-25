<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('product_brand_category', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_brand_id')->constrained('product_brands')->cascadeOnDelete();
        $table->foreignId('product_category_id')->constrained('product_categories')->cascadeOnDelete();
        $table->unique(['product_brand_id', 'product_category_id']);
    });

    // Migrasikan data category_id lama (kalau ada) ke tabel pivot
    $brands = DB::table('product_brands')->whereNotNull('category_id')->get();
    foreach ($brands as $brand) {
        DB::table('product_brand_category')->insert([
            'product_brand_id' => $brand->id,
            'product_category_id' => $brand->category_id,
        ]);
    }
}

public function down(): void
{
    Schema::dropIfExists('product_brand_category');
}
};
