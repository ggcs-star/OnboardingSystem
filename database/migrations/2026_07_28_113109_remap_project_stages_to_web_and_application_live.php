<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('projects')->where('current_stage', 'training')->update(['current_stage' => 'web_live']);
        DB::table('projects')->where('current_stage', 'live')->update(['current_stage' => 'application_live']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('projects')->where('current_stage', 'web_live')->update(['current_stage' => 'training']);
        DB::table('projects')->where('current_stage', 'application_live')->update(['current_stage' => 'live']);
    }
};
