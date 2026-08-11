<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_products', function (Blueprint $table) {
            $table->unsignedInteger('brand_slots')->default(1)->after('status');
        });

        // Clients who already onboarded more than one brand under the old
        // (unlimited) behaviour keep their existing brands usable — bump
        // their slot count to match instead of retroactively locking them out.
        DB::table('client_products')
            ->join('projects', function ($join) {
                $join->on('projects.client_id', '=', 'client_products.client_id')
                    ->on('projects.product_id', '=', 'client_products.product_id');
            })
            ->select('client_products.id', DB::raw('COUNT(projects.id) as brand_count'))
            ->groupBy('client_products.id')
            ->havingRaw('COUNT(projects.id) > 1')
            ->get()
            ->each(function ($row) {
                DB::table('client_products')->where('id', $row->id)->update(['brand_slots' => $row->brand_count]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_products', function (Blueprint $table) {
            $table->dropColumn('brand_slots');
        });
    }
};
