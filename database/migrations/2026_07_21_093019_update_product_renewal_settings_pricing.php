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
        Schema::table('product_renewal_settings', function (Blueprint $table) {
            $table->decimal('monthly_amount', 12, 2)->nullable()->after('product_id');
            $table->decimal('yearly_amount', 12, 2)->nullable()->after('monthly_amount');
            $table->dropColumn(['default_plan_duration_months', 'default_renewal_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_renewal_settings', function (Blueprint $table) {
            $table->unsignedInteger('default_plan_duration_months')->nullable();
            $table->decimal('default_renewal_amount', 12, 2)->nullable();
            $table->dropColumn(['monthly_amount', 'yearly_amount']);
        });
    }
};
