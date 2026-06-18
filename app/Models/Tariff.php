<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = [
        'base_rate',
        'hourly_rate',
        'overnight_rate',
        'lost_ticket_fine',
    ];
}
