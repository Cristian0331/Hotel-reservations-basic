<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Suite Presidencial',
                'description' => 'Nuestra suite más lujosa con vista panorámica de la ciudad',
                'price' => 500.00,
                'capacity' => 4,
                'type' => 'Suite',
                'image_url' => 'https://picsum.photos/seed/suite1/800/600',
                'is_available' => true
            ],
            [
                'name' => 'Habitación Deluxe',
                'description' => 'Habitación espaciosa con todas las comodidades',
                'price' => 250.00,
                'capacity' => 2,
                'type' => 'Deluxe',
                'image_url' => 'https://picsum.photos/seed/deluxe1/800/600',
                'is_available' => true
            ],
            [
                'name' => 'Habitación Estándar',
                'description' => 'Habitación confortable y acogedora',
                'price' => 150.00,
                'capacity' => 2,
                'type' => 'Estándar',
                'image_url' => 'https://picsum.photos/seed/standard1/800/600',
                'is_available' => true
            ],
            [
                'name' => 'Suite Familiar',
                'description' => 'Perfecta para familias, con dos habitaciones conectadas',
                'price' => 350.00,
                'capacity' => 6,
                'type' => 'Suite',
                'image_url' => 'https://picsum.photos/seed/family1/800/600',
                'is_available' => true
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
