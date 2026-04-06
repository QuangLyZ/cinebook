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
        if (!Schema::hasTable('movies')) {
            Schema::create('movies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('genre')->nullable();
                $table->string('duration')->nullable();
                $table->date('release_date')->nullable();
                $table->string('director')->nullable();
                $table->text('description')->nullable();
                $table->string('poster')->nullable();
                $table->text('actors')->nullable();
                $table->string('age_limit')->nullable();
                $table->string('trailer_link')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
