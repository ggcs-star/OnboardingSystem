<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Older projects stored their brand identity in `project_name` before the
     * dedicated `brand_name` column existed. Backfill it so brand-name
     * duplicate checks are consistent across old and new projects.
     */
    public function up(): void
    {
        DB::table('projects')
            ->whereNull('brand_name')
            ->update(['brand_name' => DB::raw('project_name')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: original null state isn't recoverable without a full dump.
    }
};
