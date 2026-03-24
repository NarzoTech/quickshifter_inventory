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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('quotation_no')->change();
            $table->unique('quotation_no');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('quotation_details', function (Blueprint $table) {
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_details', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropUnique(['quotation_no']);
            $table->dropForeign(['customer_id']);
            $table->unsignedBigInteger('quotation_no')->default(1)->change();
        });
    }
};
