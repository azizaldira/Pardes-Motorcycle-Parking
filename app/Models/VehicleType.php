<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = [
        'name',
        'category',
        'slot_required',
        'status',
    ];

    protected $casts = [
        'slot_required' => 'integer',
    ];

    public function tariff()
    {
        return $this->belongsTo(Tariff::class, 'category', 'category');
    }

    public function parkingTransactions()
    {
        return $this->hasMany(ParkingTransaction::class);
    }
}
