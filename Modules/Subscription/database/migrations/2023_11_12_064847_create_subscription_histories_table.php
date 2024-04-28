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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('subscription_plan_id');
            $table->string('plan_name');
            $table->decimal('plan_price', 8, 2);
            $table->string('expiration_date');
            $table->string('expiration');
            // write an extra migrate fields here depend on your project requirement
            $table->string('status')->default('inactive');
            $table->string('payment_method');
            $table->string('payment_status')->default('inactive');
            $table->string('transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
