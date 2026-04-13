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
        Schema::create('lot_condition', function (Blueprint $table) {
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained();
            $table->primary(['lot_id', 'condition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_condition');
    }
};
