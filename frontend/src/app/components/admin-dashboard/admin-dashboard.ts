import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTabsModule } from '@angular/material/tabs';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MatTableModule } from '@angular/material/table';
import { MatTooltipModule } from '@angular/material/tooltip';
import { RoomService, Room } from '../../services/room';
import { ReservationService } from '../../services/reservation';
import { RoomFormDialogComponent } from '../room-form-dialog/room-form-dialog';

@Component({
  selector: 'app-admin-dashboard',
  imports: [
    CommonModule,
    MatTabsModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatSnackBarModule,
    MatDialogModule,
    MatTableModule,
    MatTooltipModule
  ],
  templateUrl: './admin-dashboard.html',
  styleUrl: './admin-dashboard.css'
})
export class AdminDashboardComponent implements OnInit {
  rooms: Room[] = [];
  reservations: any[] = [];
  displayedColumns: string[] = ['name', 'type', 'price', 'capacity', 'available', 'actions'];

  constructor(
    private roomService: RoomService,
    private reservationService: ReservationService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) { }

  ngOnInit(): void {
    this.loadData();
  }

  loadData(): void {
    this.roomService.getRooms().subscribe({
      next: (rooms) => {
        this.rooms = rooms;
        console.log('Rooms loaded:', rooms);
      },
      error: (error) => {
        console.error('Error loading rooms:', error);
        this.snackBar.open('Error al cargar habitaciones', 'Cerrar', { duration: 3000 });
      }
    });

    this.reservationService.getReservations().subscribe({
      next: (reservations) => {
        this.reservations = reservations;
      },
      error: (error) => {
        console.error('Error loading reservations:', error);
      }
    });
  }

  openCreateDialog(): void {
    const dialogRef = this.dialog.open(RoomFormDialogComponent, {
      width: '600px',
      data: { mode: 'create' }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.roomService.createRoom(result).subscribe({
          next: () => {
            this.snackBar.open('✅ Habitación creada exitosamente', 'Cerrar', {
              duration: 3000,
              panelClass: ['success-snackbar']
            });
            this.loadData();
          },
          error: (error) => {
            console.error('Error creating room:', error);
            this.snackBar.open('❌ Error al crear habitación', 'Cerrar', {
              duration: 3000,
              panelClass: ['error-snackbar']
            });
          }
        });
      }
    });
  }

  openEditDialog(room: Room): void {
    const dialogRef = this.dialog.open(RoomFormDialogComponent, {
      width: '600px',
      data: { mode: 'edit', room: { ...room } }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result && room.id) {
        this.roomService.updateRoom(room.id, result).subscribe({
          next: () => {
            this.snackBar.open('✅ Habitación actualizada exitosamente', 'Cerrar', {
              duration: 3000,
              panelClass: ['success-snackbar']
            });
            this.loadData();
          },
          error: (error) => {
            console.error('Error updating room:', error);
            this.snackBar.open('❌ Error al actualizar habitación', 'Cerrar', {
              duration: 3000,
              panelClass: ['error-snackbar']
            });
          }
        });
      }
    });
  }

  deleteRoom(id: number): void {
    if (confirm('⚠️ ¿Estás seguro de que deseas eliminar esta habitación? Esta acción no se puede deshacer.')) {
      this.roomService.deleteRoom(id).subscribe({
        next: () => {
          this.snackBar.open('✅ Habitación eliminada correctamente', 'Cerrar', {
            duration: 3000,
            panelClass: ['success-snackbar']
          });
          this.loadData();
        },
        error: (error) => {
          console.error('Error deleting room:', error);
          this.snackBar.open('❌ Error al eliminar habitación', 'Cerrar', {
            duration: 3000,
            panelClass: ['error-snackbar']
          });
        }
      });
    }
  }
}
