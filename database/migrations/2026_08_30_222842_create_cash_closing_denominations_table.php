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
    Schema::create('cash_closing_denominations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cash_closing_id')->constrained()->cascadeOnDelete();
        $table->unsignedInteger('denomination');
        $table->unsignedInteger('count')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('cash_closing_denominations');
}
};
