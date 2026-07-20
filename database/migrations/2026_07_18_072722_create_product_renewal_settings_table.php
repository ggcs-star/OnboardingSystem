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
        Schema::create('product_renewal_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('default_plan_duration_months')->nullable();
            $table->decimal('default_renewal_amount', 12, 2)->nullable();
            $table->unsignedInteger('reminder_before_days')->default(30);
            $table->boolean('auto_renew_reminder')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_renewal_settings');
    }
};
