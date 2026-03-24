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
        Schema::table('quotation_details', function (Blueprint $table) {
            $table->decimal('quantity', 12, 2)->change();
            $table->decimal('price', 12, 2)->change();
            $table->decimal('sub_total', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_details', function (Blueprint $table) {
            $table->string('quantity')->change();
            $table->string('price')->change();
            $table->string('sub_total')->change();
        });
    }
};
