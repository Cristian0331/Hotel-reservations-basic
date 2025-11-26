import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { Router, RouterModule } from '@angular/router';
import { RoomService, Room } from '../../services/room';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-rooms',
  imports: [CommonModule, MatCardModule, MatButtonModule, MatIconModule, MatDialogModule, MatSnackBarModule, MatProgressSpinnerModule, RouterModule],
  templateUrl: './rooms.html',
  styleUrl: './rooms.css'
})
export class RoomsComponent implements OnInit {
  rooms: Room[] = [];
  loading = true;

  constructor(
    private roomService: RoomService,
    public authService: AuthService,
    private router: Router,
    private snackBar: MatSnackBar
  ) { }

  ngOnInit(): void {
    this.loadRooms();
  }

  loadRooms(): void {
    this.loading = true;
    console.log('Fetching rooms from API...');
    this.roomService.getRooms().subscribe({
      next: (rooms) => {
        console.log('Rooms received:', rooms);
        this.rooms = rooms;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading rooms:', error);
        this.loading = false;
        this.snackBar.open('Error al cargar habitaciones. Por favor, verifica la conexión con el servidor.', 'Cerrar', {
          duration: 5000,
          panelClass: ['error-snackbar']
        });
      }
    });
  }

  reserveRoom(room: Room): void {
    if (!this.authService.isLoggedIn()) {
      this.snackBar.open('Debes iniciar sesión para reservar', 'Cerrar', { duration: 3000 });
      this.router.navigate(['/login']);
      return;
    }
    this.router.navigate(['/reservations'], { state: { room } });
  }
}
