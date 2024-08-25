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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->float('opening_balance')->default(0);
            $table->float('closing_balance')->default(0);
            $table->float('debit_amount')->default(0);
            $table->float('credit_amount')->default(0);
            $table->float('amount')->default(0);
            $table->float('incremental_due')->default(0);
            $table->string('invoice_type')->nullable();
            $table->text('invoice_url')->nullable();
            $table->text('invoice_no')->nullable();
            $table->string('note')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
