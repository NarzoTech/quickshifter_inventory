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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('warehouse_id');
            $table->integer('quantity')->nullable();
            $table->double('total_price')->nullable();
            $table->date('order_date')->nullable();
            $table->integer('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_details')->nullable();
            $table->double('order_discount')->nullable();
            $table->double('total_tax');
            $table->double('grand_total')->nullable();
            $table->string('notes')->nullable();
            $table->string('invoice')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->integer('currency_id')->nullable()->default(null);
            $table->double('exchange_rate')->nullable()->default(null);
            $table->double('paid_amount')->nullable();
            $table->text('sale_note')->nullable();
            $table->text('staff_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
