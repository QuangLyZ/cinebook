<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->decimal('discount_value', 10, 2)->nullable();
                $table->unsignedSmallInteger('discount_rate')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('voucher_usages')) {
            Schema::create('voucher_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('Users')->cascadeOnDelete();
                $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
                $table->string('voucher_code');
                $table->decimal('discount_amount', 10, 2);
                $table->timestamp('used_at')->useCurrent();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};
