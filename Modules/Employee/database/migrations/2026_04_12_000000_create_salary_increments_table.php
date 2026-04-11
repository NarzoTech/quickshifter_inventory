<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_increments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('previous_salary')->nullable();
            $table->integer('new_salary')->nullable();
            $table->enum('increment_type', ['amount', 'percentage']);
            $table->decimal('increment_value', 10, 2);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('incremented_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_increments');
    }
};
