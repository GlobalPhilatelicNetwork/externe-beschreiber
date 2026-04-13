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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignments')->cascadeOnDelete();
            $table->unsignedInteger('sequence_number');
            $table->foreignId('category_id')->constrained('categories');
            $table->text('description');
            $table->foreignId('catalog_type_id')->constrained('catalog_types');
            $table->string('catalog_number');
            $table->decimal('starting_price', 10, 2);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['consignment_id', 'sequence_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
