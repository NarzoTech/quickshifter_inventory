<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('product_brands');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->string('badge')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('cost_per_item')->nullable();
            $table->integer('stock')->default(0);
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->string('sku')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('qty')->default(0)->nullable();
            $table->boolean('is_featured')->default(false)->nullable();
            $table->boolean('is_bestseller')->default(false)->nullable();
            $table->boolean('is_new')->default(false)->nullable();
            $table->boolean('is_top')->default(false)->nullable();
            $table->boolean('is_hot')->default(false)->nullable();
            $table->boolean('is_exchangeable')->default(false);
            $table->boolean('is_emi')->default(false)->nullable();
            $table->boolean('is_guest_checkout')->default(false)->nullable();
            $table->date('offer_start_date')->nullable();
            $table->date('offer_end_date')->nullable();
            $table->boolean('is_cod')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->boolean('is_wholesale')->default(0);
            $table->boolean('is_pre_order')->default(0);
            $table->date('release_date')->nullable();
            $table->tinyInteger('max_product')->default(1)->nullable();
            $table->boolean('is_partial')->nullable()->default(false);
            $table->integer('partial_amount')->default(0);
            $table->string('delivery_location')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_return')->default(0)->nullable();
            $table->integer('return_policy_id')->nullable();
            $table->boolean('is_warranty')->default(0)->nullable();
            $table->string('warranty_duration')->nullable();
            $table->boolean('show_homepage')->default(0)->nullable();
            $table->boolean('is_approved')->default(0)->nullable();
            $table->boolean('is_flash_deal')->default(0)->nullable();
            $table->boolean('buyone_getone')->default(0)->nullable();
            $table->unsignedBigInteger('viewed')->default(0);
            $table->unsignedBigInteger("unit_type_id")->nullable();
            $table->string("barcode_type", 255)->nullable();
            $table->string("model_number", 255)->nullable();
            $table->string("tax_type", 50)->nullable();
            $table->double("tax", 16, 2)->default(0)->nullable();
            $table->string("video_link", 255)->nullable();
            $table->Integer("minimum_order_qty")->nullable();
            $table->boolean('is_physical')->default(0)->nullable();
            $table->unsignedInteger('requested_by')->nullable();
            $table->string('stock_manage')->default(0)->nullable();
            $table->timestamp('recent_view')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
