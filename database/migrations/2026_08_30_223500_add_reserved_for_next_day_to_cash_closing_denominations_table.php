<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_closing_denominations', function (Blueprint $table) {
            $table->unsignedInteger('reserved_for_next_day')->default(0)->after('count');
        });
    }

    public function down(): void
    {
        Schema::table('cash_closing_denominations', function (Blueprint $table) {
            $table->dropColumn('reserved_for_next_day');
        });
    }
};