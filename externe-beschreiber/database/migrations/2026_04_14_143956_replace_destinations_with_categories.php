<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create new pivot table lot_destination_category referencing categories
        Schema::create('lot_destination_category', function (Blueprint $table) {
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['lot_id', 'category_id']);
        });

        // Drop old pivot and destinations table
        Schema::dropIfExists('lot_destination');
        Schema::dropIfExists('destinations');
    }

    public function down(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name_de');
            $table->string('name_en');
            $table->timestamps();
        });

        Schema::create('lot_destination', function (Blueprint $table) {
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->primary(['lot_id', 'destination_id']);
        });

        Schema::dropIfExists('lot_destination_category');
    }
};
