<?php

use App\Models\User;
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
        Schema::create('appointment_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'doctor_id')->constrained()->cascadeOnDelete();
            $table->string('appoint_cat_name')->unique();
            $table->string('appoint_cat_slug')->unique();
            $table->string('img');
            $table->double('price',2)->nullable();
            $table->text('appoint_cat_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_categories');
    }
};
