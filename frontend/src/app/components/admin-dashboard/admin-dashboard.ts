import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTabsModule } from '@angular/material/tabs';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { RoomService, Room } from '../../services/room';
import { ReservationService } from '../../services/reservation';

@Component({
  selector: 'app-admin-dashboard',
  imports: [CommonModule, MatTabsModule, MatCardModule, MatButtonModule, MatIconModule, MatSnackBarModule],
  templateUrl: './admin-dashboard.html',
  styleUrl: './admin-dashboard.css'
})
export class AdminDashboardComponent implements OnInit {
  rooms: Room[] = [];
  reservations: any[] = [];

  constructor(private roomService: RoomService, private reservationService: ReservationService, private snackBar: MatSnackBar) { }

  ngOnInit(): void {
    this.loadData();
  }

  loadData(): void {
    this.roomService.getRooms().subscribe({ next: (rooms) => { this.rooms = rooms; } });
    this.reservationService.getReservations().subscribe({ next: (reservations) => { this.reservations = reservations; } });
  }

  deleteRoom(id: number): void {
    if (confirm('¿Eliminar esta habitación?')) {
      this.roomService.deleteRoom(id).subscribe({
        next: () => {
          this.snackBar.open('Habitación eliminada', 'Cerrar', { duration: 3000 });
          this.loadData();
        }
      });
    }
  }
}
