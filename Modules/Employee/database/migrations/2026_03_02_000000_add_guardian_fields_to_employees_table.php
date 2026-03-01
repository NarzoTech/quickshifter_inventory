<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('guardian_name')->nullable()->after('nid');
            $table->string('guardian_mobile')->nullable()->after('guardian_name');
            $table->string('guardian_relation')->nullable()->after('guardian_mobile');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['guardian_name', 'guardian_mobile', 'guardian_relation']);
        });
    }
};
