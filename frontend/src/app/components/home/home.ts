import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';

@Component({
  selector: 'app-home',
  imports: [CommonModule, RouterModule, MatButtonModule, MatIconModule, MatCardModule],
  templateUrl: './home.html',
  styleUrl: './home.css'
})
export class HomeComponent {
  features = [
    { icon: 'hotel', title: 'Habitaciones de Lujo', description: 'Disfruta de nuestras elegantes habitaciones con todas las comodidades' },
    { icon: 'restaurant', title: 'Gastronomía Exquisita', description: 'Restaurante gourmet con chefs de clase mundial' },
    { icon: 'spa', title: 'Spa & Bienestar', description: 'Relájate en nuestro spa de lujo con tratamientos exclusivos' },
    { icon: 'pool', title: 'Piscina Infinita', description: 'Piscina con vista panorámica de la ciudad' },
    { icon: 'fitness_center', title: 'Gym Premium', description: 'Gimnasio totalmente equipado para tu entrenamiento' },
    { icon: 'meeting_room', title: 'Salas de Conferencias', description: 'Espacios modernos para eventos y reuniones' }
  ];
}
