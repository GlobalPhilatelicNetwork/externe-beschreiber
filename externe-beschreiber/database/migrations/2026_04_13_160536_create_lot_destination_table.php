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
        Schema::create('lot_destination', function (Blueprint $table) {
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained();
            $table->primary(['lot_id', 'destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_destination');
    }
};
