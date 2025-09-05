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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->dateTime('check_in_date');
            $table->dateTime('check_out_date');
            $table->unsignedInteger('total_night');
            $table->string('coupon')->nullable();
            $table->unsignedBigInteger('discount')->nullable();
            $table->unsignedBigInteger('total');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
