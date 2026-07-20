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
        Schema::create('renewal_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renewal_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('payment_mode')->nullable();
            $table->unsignedInteger('plan_duration_months')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_history');
    }
};
