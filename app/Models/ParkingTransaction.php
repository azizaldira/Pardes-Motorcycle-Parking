<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingTransaction extends Model
{
    protected $fillable = [
        'plate_number',
        'entry_user_id',
        'exit_user_id',
        'vehicle_type',
        'vehicle_type_id',
        'entry_time',
        'exit_time',
        'parking_duration',
        'parking_fee',
        'payment_method',
        'lost_ticket_fine_amount',
        'lost_ticket',
        'status',
        'slot_used',
        'ticket_number',
        'qr_code',
        'payment_status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'lost_ticket' => 'boolean',
        'slot_used' => 'integer',
    ];

    public function entryUser()
    {
        return $this->belongsTo(User::class, 'entry_user_id');
    }

    public function exitUser()
    {
        return $this->belongsTo(User::class, 'exit_user_id');
    }

    public function getFormattedPlateAttribute()
    {
        // Format e.g., "B1234KNL" -> "B 1234 KNL"
        // This regex matches letters at the start, numbers in middle, letters at the end
        if (preg_match('/^([A-Z]+)(\d+)([A-Z]*)$/', $this->plate_number, $matches)) {
            $formatted = $matches[1] . ' ' . $matches[2];
            if (!empty($matches[3])) {
                $formatted .= ' ' . $matches[3];
            }
            return $formatted;
        }
        
        return $this->plate_number;
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
