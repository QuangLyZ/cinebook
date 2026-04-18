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
        // Add missing columns to cinemas table if they don't exist yet
        if (Schema::hasTable('cinemas')) {
            Schema::table('cinemas', function (Blueprint $table) {
                if (! Schema::hasColumn('cinemas', 'district')) {
                    $table->string('district')->nullable()->after('address');
                }
                if (! Schema::hasColumn('cinemas', 'phone')) {
                    $table->string('phone')->nullable()->after('district');
                }
                if (! Schema::hasColumn('cinemas', 'hours')) {
                    $table->string('hours')->nullable()->after('phone');
                }
                if (! Schema::hasColumn('cinemas', 'screens')) {
                    $table->integer('screens')->default(0)->after('hours');
                }
                if (! Schema::hasColumn('cinemas', 'seats')) {
                    $table->integer('seats')->default(0)->after('screens');
                }
                if (! Schema::hasColumn('cinemas', 'features')) {
                    $table->json('features')->nullable()->after('seats');
                }
                if (! Schema::hasColumn('cinemas', 'image')) {
                    $table->string('image')->nullable()->after('features');
                }
                if (! Schema::hasColumn('cinemas', 'map')) {
                    $table->string('map')->nullable()->after('image');
                }
                if (! Schema::hasColumn('cinemas', 'status')) {
                    $table->string('status')->default('open')->after('map');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cinemas')) {
            Schema::table('cinemas', function (Blueprint $table) {
                foreach ([
                    'district', 'phone', 'hours', 'screens',
                    'seats', 'features', 'image', 'map', 'status',
                ] as $col) {
                    if (Schema::hasColumn('cinemas', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
