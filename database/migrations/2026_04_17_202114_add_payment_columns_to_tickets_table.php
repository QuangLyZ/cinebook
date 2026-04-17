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
        Schema::table('tickets', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->string('voucher_code')->nullable();
            $table->string('reference_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'final_price', 'voucher_code', 'reference_code']);
        });
    }
};
