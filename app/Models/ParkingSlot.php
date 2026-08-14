<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    protected $fillable = [
        'name',
        'total_slots',
        'status',
    ];

    protected $casts = [
        'total_slots' => 'integer',
    ];
}
