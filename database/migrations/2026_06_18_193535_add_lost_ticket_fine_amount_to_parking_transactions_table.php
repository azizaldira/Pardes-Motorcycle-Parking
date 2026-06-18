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
        Schema::table('parking_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('lost_ticket_fine_amount')->default(0)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_transactions', function (Blueprint $table) {
            $table->dropColumn('lost_ticket_fine_amount');
        });
    }
};
