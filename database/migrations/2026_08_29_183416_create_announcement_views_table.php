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
    Schema::create('announcement_views', function (Blueprint $table) {
        $table->id();
        $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->date('viewed_date');
        $table->timestamps();

        $table->unique(['announcement_id', 'user_id', 'viewed_date']);
    });
}

public function down(): void
{
    Schema::dropIfExists('announcement_views');
}
};
