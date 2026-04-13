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
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignor_number');
            $table->string('internal_nid');
            $table->unsignedInteger('start_number');
            $table->unsignedInteger('next_number');
            $table->foreignId('catalog_part_id')->constrained('catalog_parts');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignments');
    }
};
