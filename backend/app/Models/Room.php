<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'capacity',
        'type',
        'image_url',
        'is_available',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
