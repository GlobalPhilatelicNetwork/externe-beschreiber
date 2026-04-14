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
        Schema::create('category_catalog_type_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('category_prefix');
            $table->foreignId('catalog_type_id')->constrained('catalog_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('category_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_catalog_type_mappings');
    }
};
