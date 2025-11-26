import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { ReservationService } from '../../services/reservation';
import { Room } from '../../services/room';

@Component({
  selector: 'app-reservations',
  imports: [CommonModule, ReactiveFormsModule, MatCardModule, MatFormFieldModule, MatInputModule, MatButtonModule, MatDatepickerModule, MatNativeDateModule, MatSnackBarModule],
  templateUrl: './reservations.html',
  styleUrl: './reservations.css'
})
export class ReservationsComponent implements OnInit {
  reservationForm: FormGroup;
  reservations: any[] = [];
  selectedRoom: Room | null = null;
  loading = false;

  constructor(private fb: FormBuilder, private reservationService: ReservationService, private router: Router, private snackBar: MatSnackBar) {
    this.reservationForm = this.fb.group({
      room_id: ['', Validators.required],
      check_in: ['', Validators.required],
      check_out: ['', Validators.required],
      total_price: [0, Validators.required]
    });

    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state) {
      this.selectedRoom = navigation.extras.state['room'];
      if (this.selectedRoom) {
        this.reservationForm.patchValue({ room_id: this.selectedRoom.id, total_price: this.selectedRoom.price });
      }
    }
  }

  ngOnInit(): void {
    this.loadReservations();
  }

  loadReservations(): void {
    this.reservationService.getReservations().subscribe({
      next: (reservations) => { this.reservations = reservations; },
      error: (error) => { this.snackBar.open('Error al cargar reservas', 'Cerrar', { duration: 3000 }); }
    });
  }

  onSubmit(): void {
    if (this.reservationForm.valid) {
      this.loading = true;
      this.reservationService.createReservation(this.reservationForm.value).subscribe({
        next: () => {
          this.snackBar.open('¡Reserva creada exitosamente!', 'Cerrar', { duration: 3000 });
          this.loadReservations();
          this.reservationForm.reset();
          this.loading = false;
        },
        error: (error) => {
          this.loading = false;
          this.snackBar.open('Error al crear reserva', 'Cerrar', { duration: 3000 });
        }
      });
    }
  }
}
