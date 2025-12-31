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
        // Check if admin_route_prefix already exists
        $exists = DB::table('settings')->where('key', 'admin_route_prefix')->exists();

        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'admin_route_prefix',
                'value' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'admin_route_prefix')->delete();
    }
};
