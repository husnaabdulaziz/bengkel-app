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
        Schema::create('cash_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('closing_date');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('cash_sales', 15, 2)->default(0);
            $table->decimal('cash_expenses', 15, 2)->default(0);
            $table->decimal('expected_balance', 15, 2)->default(0);
            $table->decimal('actual_balance', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'closing_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closings');
    }
};
