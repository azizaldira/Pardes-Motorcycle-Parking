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
            $table->foreignId('vehicle_type_id')->nullable()->after('vehicle_type')->constrained('vehicle_types')->nullOnDelete();
            $table->unsignedInteger('slot_used')->nullable()->after('vehicle_type_id');
            $table->string('ticket_number')->nullable()->unique()->after('id');
            $table->text('qr_code')->nullable()->after('ticket_number');
            $table->string('payment_status')->default('pending')->after('status'); // Values: pending, paid, cancelled
            $table->string('midtrans_order_id')->nullable()->index()->after('payment_status');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->timestamp('cancelled_at')->nullable()->after('exit_time');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_transactions', function (Blueprint $table) {
            $table->dropForeign(['vehicle_type_id']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'vehicle_type_id',
                'slot_used',
                'ticket_number',
                'qr_code',
                'payment_status',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'cancelled_at',
                'cancelled_by',
            ]);
        });
    }
};
