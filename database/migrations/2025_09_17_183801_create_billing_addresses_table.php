<?php

use App\Models\Ecommerce\Order;
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
        Schema::create('billing_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class, 'order_id')->constrained()->cascadeOnDelete();
            $table->string('bil_country')->nullable()->default('Philippines');
            $table->string('bil_province')->nullable()->default('Negros Occidental');
            $table->string('street')->nullable();
            $table->string('bil_city')->nullable();
            $table->string('bil_barangay')->nullable();
            //$table->string('zip')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('bil_complete_address')
                ->storedAs('CONCAT(street," ",bil_barangay," ", bil_city, " ", postal_code," ", bil_province, " ", bil_country)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_addresses');
    }
};
