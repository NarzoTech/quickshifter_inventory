<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\PageBuilder\app\Models\CustomizeablePage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customizeable_page_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CustomizeablePage::class);
            $table->string('title');
            $table->string('component_name')->nullable();
            $table->integer('position');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customizeable_page_items');
    }
};
