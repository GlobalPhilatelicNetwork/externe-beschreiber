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
        Schema::table('lots', function (Blueprint $table) {
            $table->enum('lot_type', ['single', 'collection'])->default('single')->after('sequence_number');
            $table->foreignId('grouping_category_id')->nullable()->constrained('grouping_categories')->nullOnDelete()->after('lot_type');
            $table->text('provenance')->nullable()->after('description');
            $table->string('epos')->nullable()->after('provenance');

            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropForeign(['catalog_type_id']);
            $table->dropColumn(['catalog_type_id', 'catalog_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('catalog_type_id')->constrained('catalog_types');
            $table->string('catalog_number');
            $table->dropForeign(['grouping_category_id']);
            $table->dropColumn(['lot_type', 'grouping_category_id', 'provenance', 'epos']);
        });
    }
};
