<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Product::all()->each(function (Product $product) {
            $product->subscriptionPlans()->delete();

            $product->subscriptionPlans()->create([
                'name' => 'Yearly',
                'duration_months' => 12,
                'amount' => 90000,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
