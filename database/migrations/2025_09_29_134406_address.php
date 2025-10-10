<?php

use App\Models\User;
use App\Models\Ecommerce\Order;
use Illuminate\Support\Facades\DB;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class, 'order_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            $table->string('country')->nullable()->default('Philippines');
            $table->string('province')->nullable()->default('Negros Occidental');
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('postal_code')->nullable();
           // $table->enum('address_type', ['billing', 'shipping'])->nullable(); // Add Address Type
            $table->string('complete_address')
                ->storedAs('CONCAT(street, " ",barangay," ", city, " ", postal_code , " ", province, " ", country)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
