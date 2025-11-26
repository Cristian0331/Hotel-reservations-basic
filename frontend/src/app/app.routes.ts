import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { RegisterComponent } from './components/register/register';
import { HomeComponent } from './components/home/home';
import { RoomsComponent } from './components/rooms/rooms';
import { ReservationsComponent } from './components/reservations/reservations';
import { AdminDashboardComponent } from './components/admin-dashboard/admin-dashboard';

export const routes: Routes = [
    { path: '', component: HomeComponent },
    { path: 'login', component: LoginComponent },
    { path: 'register', component: RegisterComponent },
    { path: 'rooms', component: RoomsComponent },
    { path: 'reservations', component: ReservationsComponent },
    { path: 'admin', component: AdminDashboardComponent },
    { path: '**', redirectTo: '' }
];
