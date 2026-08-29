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
    DB::statement("ALTER TABLE purchases MODIFY COLUMN status VARCHAR(20) DEFAULT 'completed'");
    DB::statement("ALTER TABLE purchases MODIFY COLUMN invoice_number VARCHAR(191) NULL");
}

public function down(): void
{
    DB::statement("ALTER TABLE purchases MODIFY COLUMN invoice_number VARCHAR(191) NOT NULL");
}
};
