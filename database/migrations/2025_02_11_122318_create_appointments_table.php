<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Appointment\AppointmentCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('appoint_cat_id')->constrained('appointment_categories')->onDelete('cascade');
           // $table->foreignIdFor(AppointmentCategory::class, 'appointment_category_id')->constrained()->cascadeOnDelete();
            $table->string('pet_name');
            $table->enum('pet_type', ['dog', 'cat', 'other'])->default('dog');
            $table->string('pet_breed');
            $table->enum('pet_gender', ['male', 'female'])->default('male');
            $table->enum('appointment_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('pet_age');
            $table->string('advance_payment_method')->nullable();
            $table->string('pet_weight');
            $table->boolean('isPetVaccinated')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
