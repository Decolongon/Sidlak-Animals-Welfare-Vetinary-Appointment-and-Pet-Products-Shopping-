<?php

use App\Models\Ecommerce\Product;
use App\Models\Ecommerce\ProductDiscount;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class,'product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductDiscount::class,'product_discount_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('discount_code')->nullable()->index()->unique();
            $table->enum('discount_type', ['fixed', 'percent'])->default('percent');
            $table->decimal('discounted_price',10,2)->nullable(); //by percent like example 2percent off
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_code');
    }
};
