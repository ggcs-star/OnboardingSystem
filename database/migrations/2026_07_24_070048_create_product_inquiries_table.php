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
        Schema::create('product_inquiries', function (Blueprint $table) {

    $table->id();

    $table->foreignId('client_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('contact_person');

    $table->string('email');

    $table->string('phone')->nullable();

    $table->string('company')->nullable();

    $table->text('message')->nullable();

    $table->enum('status',[
        'pending',
        'approved',
        'rejected'
    ])->default('pending');

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inquiries');
    }
};
