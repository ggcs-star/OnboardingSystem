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
        Schema::table('product_document_fields', function (Blueprint $table) {
            $table->foreignId('product_document_group_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
            $table->dropColumn('section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_document_fields', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_document_group_id');
            $table->string('section')->nullable()->after('product_id');
        });
    }
};
