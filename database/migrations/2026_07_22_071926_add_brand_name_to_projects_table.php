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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('project_name');
            $table->unique(['client_id', 'product_id', 'brand_name'], 'projects_client_product_brand_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique('projects_client_product_brand_unique');
            $table->dropColumn('brand_name');
        });
    }
};
