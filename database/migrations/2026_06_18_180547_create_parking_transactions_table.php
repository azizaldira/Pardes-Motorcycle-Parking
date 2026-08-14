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
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->index();
            $table->foreignId('entry_user_id')->constrained('users');
            $table->foreignId('exit_user_id')->nullable()->constrained('users');
            $table->string('vehicle_type')->default('Matic');
            $table->dateTime('entry_time')->index();
            $table->dateTime('exit_time')->nullable();
            $table->integer('parking_duration')->nullable(); // in minutes
            $table->unsignedBigInteger('parking_fee')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('lost_ticket')->default(false);
            $table->string('status')->default('parked')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
