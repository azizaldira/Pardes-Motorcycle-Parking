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
        'entry_time',
        'exit_time',
        'parking_duration',
        'parking_fee',
        'payment_method',
        'lost_ticket',
        'status',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'lost_ticket' => 'boolean',
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
}
