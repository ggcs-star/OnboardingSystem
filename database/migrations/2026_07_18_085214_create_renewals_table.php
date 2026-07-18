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
        Schema::create('renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('plan_name')->nullable();
            $table->unsignedInteger('plan_duration_months')->nullable();
            $table->date('go_live_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('renewal_amount', 12, 2)->nullable();
            $table->unsignedInteger('reminder_before_days')->default(30);
            $table->string('status')->default('active');
            $table->timestamp('renewed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewals');
    }
};
