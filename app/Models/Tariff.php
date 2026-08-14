<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = [
        'category',
        'base_rate',
        'hourly_rate',
        'overnight_rate',
        'lost_ticket_fine',
    ];

    public function vehicleTypes()
    {
        return $this->hasMany(VehicleType::class, 'category', 'category');
    }

    public function getSlotRequiredAttribute(): int
    {
        return match($this->category) {
            'Golongan I' => 1,
            'Golongan II' => 2,
            'Golongan III' => 3,
            default => 1,
        };
    }
}

