# 🔌 Cómo Se Consumieron las APIs - Guía Técnica

## 📋 Índice
0. [¿Qué es Consumir una API?](#qué-es-consumir-una-api) - **EMPIEZA AQUÍ si eres principiante**
1. [Arquitectura General](#arquitectura-general)
2. [Sistema de Autenticación](#sistema-de-autenticación)
3. [Consumo de APIs por Servicio](#consumo-de-apis-por-servicio)
4. [Flujo Completo de una Petición](#flujo-completo-de-una-petición)
5. [Manejo de Estados y Errores](#manejo-de-estados-y-errores)

---

## 🎯 ¿Qué es Consumir una API?

### Explicación Simple

**Consumir una API** significa que tu **Frontend** (la página web que ve el usuario) le **pide información** al **Backend** (el servidor) a través de internet.

#### Analogía del Restaurante:
```
Frontend = Tú en un restaurante (el cliente)
Backend = La cocina del restaurante  
API = El mesero que lleva tu pedido

"Consumir la API" = Pedirle comida al mesero
```

---

### Los 3 Pasos para Consumir una API

#### **PASO 1: El Frontend HACE LA PETICIÓN**

En tu proyecto, esto se hace en los **SERVICIOS**:

```typescript
// frontend/src/app/services/room.ts

getRooms(): Observable<Room[]> {
  // Aquí CONSUMES la API ↓
  return this.http.get<Room[]>('http://localhost:8000/api/rooms');
  //            ↑           ↑                    ↑
  //        Método GET   Tipo de dato      URL del servidor
}
```

**¿Qué significa esto?**
- `http.get()` = "Haz una petición GET" (GET = "Dame información")
- `'http://localhost:8000/api/rooms'` = Dirección del servidor
- Es como decir: **"Oye servidor, dame la lista de habitaciones"**

---

#### **PASO 2: El Backend PROCESA y RESPONDE**

Laravel recibe tu petición y ejecuta código:

```php
// backend/app/Http/Controllers/RoomController.php

public function index() {
    // Busca TODAS las habitaciones en la base de datos
    $rooms = Room::all();
    
    // Las convierte automáticamente a JSON y las devuelve
    return $rooms;
}
```

---

#### **PASO 3: El Frontend RECIBE y USA los datos**

Los componentes se **suscriben** para recibir la respuesta:

```typescript
// frontend/src/app/components/rooms/rooms.ts

loadRooms(): void {
  // CONSUMIMOS la API ↓
  this.roomService.getRooms().subscribe({
    next: (rooms) => {
      // ✅ ÉXITO: Aquí recibimos las habitaciones
      console.log('Habitaciones:', rooms);
      this.rooms = rooms;  // Las guardamos
      // Angular automáticamente las muestra en pantalla
    },
    error: (error) => {
      // ❌ ERROR: Algo salió mal
      console.error('Error:', error);
      this.snackBar.open('Error al cargar habitaciones');
    }
  });
}
```

---

### 🔄 Flujo Visual Simplificado

```
┌─────────────────────────────────────────────────────────────┐
│  1. USUARIO hace click en "Ver Habitaciones"               │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  2. COMPONENTE llama al SERVICIO                            │
│     this.roomService.getRooms()                             │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  3. SERVICIO hace petición HTTP                             │
│     GET http://localhost:8000/api/rooms                     │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
                 🌐 INTERNET
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  4. BACKEND (Laravel) busca en la base de datos             │
│     SELECT * FROM rooms                                     │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  5. BACKEND devuelve JSON                                   │
│     [{ id: 1, name: "Suite" }, { id: 2, name: "Doble" }]   │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
                 🌐 INTERNET
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  6. SERVICIO recibe la respuesta                            │
│     Observable emite los datos                              │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  7. COMPONENTE muestra las habitaciones                     │
│     this.rooms = [...datos recibidos...]                    │
└─────────────────────┬───────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────────────────┐
│  8. USUARIO VE las habitaciones en pantalla ✅              │
└─────────────────────────────────────────────────────────────┘
```

---

### 💡 Ejemplos Prácticos de Tu Proyecto

#### **Ejemplo 1: Listar Habitaciones (GET)**

**Frontend pide:**
```typescript
this.http.get('http://localhost:8000/api/rooms')
```

**Backend responde:**
```json
[
  {
    "id": 1,
    "name": "Suite Presidencial",
    "price": 350,
    "capacity": 4,
    "type": "Suite"
  },
  {
    "id": 2,
    "name": "Habitación Doble",
    "price": 120,
    "capacity": 2,
    "type": "Estándar"
  }
]
```

**Frontend muestra:** Cards con las habitaciones

---

#### **Ejemplo 2: Iniciar Sesión (POST)**

**Frontend envía:**
```typescript
this.http.post('http://localhost:8000/api/login', {
  email: 'maria@gmail.com',
  password: 'password123'
})
```

**Backend responde:**
```json
{
  "access_token": "1|abcd1234567890",
  "user": {
    "id": 1,
    "name": "María García",
    "role": "user"
  }
}
```

**Frontend guarda:** El token y redirige

---

#### **Ejemplo 3: Crear Habitación (POST con autenticación)**

**Frontend envía:**
```typescript
this.http.post('http://localhost:8000/api/rooms', {
  name: 'Suite VIP',
  price: 500,
  capacity: 6
})
```

**Interceptor agrega automáticamente:**
```http
Authorization: Bearer 1|abcd1234567890
```

**Backend verifica token, crea y responde:**
```json
{
  "id": 15,
  "name": "Suite VIP",
  "price": 500,
  "capacity": 6
}
```

---

### 🛠️ Los 4 Tipos de Peticiones HTTP

| Método | Para qué sirve | Ejemplo en el proyecto |
|--------|----------------|------------------------|
| **GET** | **Obtener** datos | Listar habitaciones |
| **POST** | **Crear** datos nuevos | Registrarse, hacer reserva |
| **PUT** | **Actualizar** datos | Cambiar precio de habitación |
| **DELETE** | **Eliminar** datos | Cancelar reserva |

---

### 📦 ¿Qué es JSON?

**JSON** (JavaScript Object Notation) es el "idioma" en que hablan Frontend y Backend.

**Ejemplo:**
```json
{
  "name": "Suite Presidencial",
  "price": 350,
  "capacity": 4,
  "is_available": true
}
```

Es como un **formulario estructurado** con pares de nombre-valor.

---

### 🔐 Autenticación con Token

Algunas APIs necesitan que estés **logueado**:

#### **1. Login → Recibes TOKEN**
```
POST /api/login → Respuesta: { token: "1|abc..." }
```

#### **2. Guardas el token**
```typescript
localStorage.setItem('token', '1|abc...')
```

#### **3. Interceptor lo agrega AUTOMÁTICAMENTE**
```typescript
headers.set('Authorization', 'Bearer 1|abc...')
```

#### **4. Backend verifica**
```php
if (token válido) → ✅ Permite continuar
if (token inválido) → ❌ Error 401
```

---

### 📚 Resumen: ¿Qué necesitas para consumir una API?

✅ **Un SERVICIO** que hace la petición
```typescript
this.http.get('URL')
```

✅ **Un COMPONENTE** que se suscribe
```typescript
service.getData().subscribe(...)
```

✅ **Un BACKEND** que responde
```php
return $data;
```

---

## 📝 Archivos Clave en tu Proyecto

### **Que CONSUMEN APIs (Frontend):**
- `frontend/src/app/services/auth.ts` - Login, register, logout
- `frontend/src/app/services/room.ts` - CRUD de habitaciones
- `frontend/src/app/services/reservation.ts` - CRUD de reservas

### **Que PROVEEN APIs (Backend):**
- `backend/routes/api.php` - Define las rutas
- `backend/app/Http/Controllers/*.php` - Procesan las peticiones

---

## 🏗️ Arquitectura General

### Stack Tecnológico

**Backend (Servidor)**
```
Laravel 11 (PHP 8.2+)
├── API REST
├── Laravel Sanctum (Autenticación)
├── PostgreSQL (Base de Datos)
└── Eloquent ORM
```

**Frontend (Cliente)**
```
Angular 19 (TypeScript 5.6)
├── HttpClient (Peticiones HTTP)
├── RxJS (Programación Reactiva)
├── Interceptores HTTP
└── Servicios Especializados
```

### Comunicación Cliente-Servidor

```
Frontend (http://localhost:4200)
    ↕ HTTP/HTTPS (JSON)
Backend (http://localhost:8000/api)
    ↕
PostgreSQL Database
```

---

## 🔐 Sistema de Autenticación

### Cómo Funciona Laravel Sanctum

**1. Registro/Login → Generación de Token**

```php
// Backend: AuthController.php
$token = $user->createToken('auth_token')->plainTextToken;
// Token generado: "1|abcd1234567890xyz..."
```

**2. Frontend Guarda el Token**

```typescript
// Frontend: auth.ts
localStorage.setItem('token', response.access_token);
```

**3. Interceptor Agrega el Token Automáticamente**

```typescript
// Frontend: auth-interceptor.ts
const token = localStorage.getItem('token');
const cloned = req.clone({
  headers: req.headers.set('Authorization', `Bearer ${token}`)
});
```

**4. Backend Verifica el Token**

```php
// Backend: api.php
Route::middleware('auth:sanctum')->group(function () {
    // Sanctum verifica el token automáticamente
    // Si es válido: permite continuar
    // Si no: devuelve 401 Unauthorized
});
```

---

## 🔌 Consumo de APIs por Servicio

### 1️⃣ AuthService - Autenticación

#### **REGISTER (Registrarse)**

**Frontend hace:**
```typescript
// services/auth.ts
register(data: any): Observable<AuthResponse> {
  return this.http.post<AuthResponse>(
    'http://localhost:8000/api/register', 
    data
  ).pipe(
    tap(response => this.handleAuth(response))
  );
}
```

**Petición HTTP real:**
```http
POST http://localhost:8000/api/register
Content-Type: application/json

{
  "name": "María García",
  "email": "maria@gmail.com",
  "password": "password123",
  "phone": "+1234567890"
}
```

**Backend responde:**
```json
{
  "access_token": "1|abcd1234567890xyz",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "María García",
    "email": "maria@gmail.com",
    "role": "user",
    "phone": "+1234567890"
  }
}
```

**Frontend procesa:**
```typescript
private handleAuth(response: AuthResponse): void {
  // Guarda en localStorage
  localStorage.setItem('token', response.access_token);
  localStorage.setItem('user', JSON.stringify(response.user));
  
  // Notifica a toda la app
  this.currentUserSubject.next(response.user);
}
```

---

#### **LOGIN (Iniciar Sesión)**

**Frontend hace:**
```typescript
login(email: string, password: string): Observable<AuthResponse> {
  return this.http.post<AuthResponse>(
    'http://localhost:8000/api/login',
    { email, password }
  ).pipe(
    tap(response => this.handleAuth(response))
  );
}
```

**Petición HTTP:**
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "maria@gmail.com",
  "password": "password123"
}
```

**Backend verifica y responde igual que en register**

---

#### **LOGOUT (Cerrar Sesión)**

**Frontend hace:**
```typescript
logout(): Observable<any> {
  return this.http.post('http://localhost:8000/api/logout', {}).pipe(
    tap(() => {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      this.currentUserSubject.next(null);
    })
  );
}
```

**Petición HTTP:**
```http
POST http://localhost:8000/api/logout
Authorization: Bearer 1|abcd1234567890xyz  ← Agregado por interceptor
```

**Backend responde:**
```json
{
  "message": "Logged out successfully"
}
```

---

### 2️⃣ RoomService - Gestión de Habitaciones

#### **GET ALL ROOMS (Listar Habitaciones)**

**Frontend hace:**
```typescript
// services/room.ts
getRooms(): Observable<Room[]> {
  return this.http.get<Room[]>(
    'http://127.0.0.1:8000/api/rooms',
    { withCredentials: true }
  );
}
```

**Petición HTTP:**
```http
GET http://127.0.0.1:8000/api/rooms
```

**Backend responde:**
```json
[
  {
    "id": 1,
    "name": "Suite Presidencial",
    "description": "Habitación de lujo con vista al mar",
    "price": 350,
    "capacity": 4,
    "type": "Suite",
    "image_url": "https://example.com/image.jpg",
    "is_available": true
  },
  {
    "id": 2,
    "name": "Habitación Doble",
    "description": "Habitación cómoda para dos personas",
    "price": 120,
    "capacity": 2,
    "type": "Estándar",
    "image_url": "https://example.com/image2.jpg",
    "is_available": true
  }
]
```

**Componente usa:**
```typescript
// components/rooms/rooms.ts
loadRooms(): void {
  this.roomService.getRooms().subscribe({
    next: (rooms) => {
      this.rooms = rooms;  // Actualiza la variable
      // Angular automáticamente actualiza la vista
    },
    error: (error) => {
      console.error('Error:', error);
      this.snackBar.open('Error al cargar habitaciones');
    }
  });
}
```

---

#### **CREATE ROOM (Crear Habitación - Admin)**

**Frontend hace:**
```typescript
createRoom(room: Room): Observable<Room> {
  return this.http.post<Room>(
    'http://127.0.0.1:8000/api/rooms',
    room,
    { withCredentials: true }
  );
}
```

**Petición HTTP:**
```http
POST http://127.0.0.1:8000/api/rooms
Authorization: Bearer 1|abcd1234567890xyz  ← Interceptor
Content-Type: application/json

{
  "name": "Suite VIP",
  "description": "La mejor habitación",
  "price": 500,
  "capacity": 6,
  "type": "Suite",
  "image_url": "https://example.com/vip.jpg",
  "is_available": true
}
```

**Backend responde:**
```json
{
  "id": 15,
  "name": "Suite VIP",
  "description": "La mejor habitación",
  "price": 500,
  "capacity": 6,
  "type": "Suite",
  "image_url": "https://example.com/vip.jpg",
  "is_available": true,
  "created_at": "2025-11-25T23:00:00.000000Z",
  "updated_at": "2025-11-25T23:00:00.000000Z"
}
```

---

#### **UPDATE ROOM (Actualizar Habitación)**

**Frontend hace:**
```typescript
updateRoom(id: number, room: Partial<Room>): Observable<Room> {
  return this.http.put<Room>(
    `http://127.0.0.1:8000/api/rooms/${id}`,
    room,
    { withCredentials: true }
  );
}
```

**Petición HTTP:**
```http
PUT http://127.0.0.1:8000/api/rooms/15
Authorization: Bearer 1|abcd1234567890xyz
Content-Type: application/json

{
  "price": 450,
  "is_available": false
}
```

**Backend responde:**
```json
{
  "id": 15,
  "name": "Suite VIP",
  "price": 450,           ← Actualizado
  "is_available": false,  ← Actualizado
  ...
}
```

---

#### **DELETE ROOM (Eliminar Habitación)**

**Frontend hace:**
```typescript
deleteRoom(id: number): Observable<void> {
  return this.http.delete<void>(
    `http://127.0.0.1:8000/api/rooms/${id}`,
    { withCredentials: true }
  );
}
```

**Petición HTTP:**
```http
DELETE http://127.0.0.1:8000/api/rooms/15
Authorization: Bearer 1|abcd1234567890xyz
```

**Backend responde:**
```http
HTTP/1.1 204 No Content
```

---

### 3️⃣ ReservationService - Gestión de Reservas

#### **GET RESERVATIONS (Listar Reservas)**

**Frontend hace:**
```typescript
getReservations(): Observable<Reservation[]> {
  return this.http.get<Reservation[]>(
    'http://localhost:8000/api/reservations'
  );
}
```

**Petición HTTP:**
```http
GET http://localhost:8000/api/reservations
Authorization: Bearer 1|abcd1234567890xyz
```

**Si eres USER normal, backend responde:**
```json
[
  {
    "id": 42,
    "user_id": 1,
    "room_id": 5,
    "check_in": "2025-12-25",
    "check_out": "2025-12-28",
    "total_price": 1050,
    "status": "pending",
    "room": {
      "id": 5,
      "name": "Suite Presidencial",
      "price": 350
    }
  }
]
```

**Si eres ADMIN, backend responde:**
```json
[
  {
    "id": 42,
    "user_id": 1,
    "room_id": 5,
    "check_in": "2025-12-25",
    "check_out": "2025-12-28",
    "total_price": 1050,
    "status": "pending",
    "user": {
      "id": 1,
      "name": "María García",
      "email": "maria@gmail.com"
    },
    "room": {
      "id": 5,
      "name": "Suite Presidencial"
    }
  },
  // ... más reservas de otros usuarios
]
```

---

#### **CREATE RESERVATION (Crear Reserva)**

**Frontend hace:**
```typescript
createReservation(reservation: Reservation): Observable<Reservation> {
  return this.http.post<Reservation>(
    'http://localhost:8000/api/reservations',
    reservation
  );
}
```

**Petición HTTP:**
```http
POST http://localhost:8000/api/reservations
Authorization: Bearer 1|abcd1234567890xyz
Content-Type: application/json

{
  "room_id": 5,
  "check_in": "2025-12-25",
  "check_out": "2025-12-28",
  "total_price": 1050
}
```

**Backend automáticamente agrega user_id del token y responde:**
```json
{
  "id": 42,
  "user_id": 1,           ← Automático del token
  "room_id": 5,
  "check_in": "2025-12-25",
  "check_out": "2025-12-28",
  "total_price": 1050,
  "status": "pending",    ← Automático
  "created_at": "2025-11-25T23:00:00.000000Z",
  "updated_at": "2025-11-25T23:00:00.000000Z"
}
```

---

## 🔄 Flujo Completo de una Petición

### Ejemplo: Usuario hace una reserva

```
1. USUARIO HACE CLICK EN "RESERVAR"
   ↓
2. COMPONENTE (reservations.ts)
   this.reservationService.createReservation(datos).subscribe(...)
   ↓
3. SERVICIO (reservation.ts)
   this.http.post('/api/reservations', datos)
   ↓
4. AUTH INTERCEPTOR (auth-interceptor.ts)
   Agrega: Authorization: Bearer {token}
   ↓
5. PETICIÓN HTTP VIAJA AL SERVIDOR
   POST http://localhost:8000/api/reservations
   Headers: { Authorization: "Bearer 1|abc..." }
   Body: { room_id: 5, check_in: "2025-12-25", ... }
   ↓
6. LARAVEL RECIBE (api.php)
   POST /api/reservations → busca la ruta
   Encuentra: Route::middleware('auth:sanctum')
   ↓
7. MIDDLEWARE SANCTUM
   Verifica el token en la base de datos
   Si es válido: identifica user_id=1 y continúa
   Si no: devuelve error 401
   ↓
8. CONTROLADOR (ReservationController.php)
   store() function
   - Valida datos
   - Crea reserva con user_id del token
   - Devuelve JSON
   ↓
9. RESPUESTA HTTP VIAJA AL FRONTEND
   Status: 201 Created
   Body: { id: 42, user_id: 1, ... }
   ↓
10. SERVICIO recibe la respuesta
    Observable emite el valor
    ↓
11. COMPONENTE procesa
    next: (response) => {
      this.snackBar.open('¡Reserva creada!');
      this.loadReservations();  // Actualiza lista
    }
    ↓
12. USUARIO VE
    ✅ Notificación: "¡Reserva creada exitosamente!"
    Lista actualizada con la nueva reserva
```

---

## 🛡️ Manejo de Estados y Errores

### Estado de Carga

**En todos los componentes:**
```typescript
export class RoomsComponent {
  rooms: Room[] = [];
  loading = true;  // ← Estado de carga
  
  loadRooms(): void {
    this.loading = true;  // Inicia loading
    
    this.roomService.getRooms().subscribe({
      next: (rooms) => {
        this.rooms = rooms;
        this.loading = false;  // Termina loading
      },
      error: (error) => {
        this.loading = false;  // Termina loading (con error)
        this.showError();
      }
    });
  }
}
```

**En el HTML:**
```html
<!-- Mostrar spinner mientras carga -->
<mat-spinner *ngIf="loading"></mat-spinner>

<!-- Mostrar contenido cuando termina -->
<div *ngIf="!loading">
  <mat-card *ngFor="let room of rooms">
    {{ room.name }}
  </mat-card>
</div>
```

---

### Manejo de Errores

**Patrón estándar en todos los servicios:**
```typescript
this.roomService.getRooms().subscribe({
  next: (data) => {
    // ✅ Éxito
    console.log('Datos recibidos:', data);
  },
  error: (error) => {
    // ❌ Error
    console.error('Error:', error);
    
    // Mostrar mensaje al usuario
    this.snackBar.open(
      'Error al cargar datos. Por favor intenta de nuevo.',
      'Cerrar',
      { duration: 5000 }
    );
  }
});
```

---

### Estado Reactivo con BehaviorSubject

**En AuthService:**
```typescript
// BehaviorSubject: mantiene el estado actual
private currentUserSubject = new BehaviorSubject<User | null>(null);

// Observable público para suscribirse
public currentUser$ = this.currentUserSubject.asObservable();

// Actualizar estado
login(...).pipe(
  tap(response => {
    this.currentUserSubject.next(response.user);  // ← Notifica a todos
  })
)
```

**En componentes:**
```typescript
export class NavbarComponent {
  user$ = this.authService.currentUser$;  // ← Se actualiza automáticamente
  
  constructor(private authService: AuthService) {}
}
```

**En el HTML:**
```html
<!-- Se actualiza automáticamente cuando cambia el usuario -->
<div *ngIf="user$ | async as user">
  Bienvenido, {{ user.name }}!
</div>
```

---

## 📊 Resumen de Endpoints Consumidos

| Servicio | Método | Endpoint | Autenticación | Descripción |
|----------|--------|----------|---------------|-------------|
| **AuthService** |
| | POST | `/api/register` | No | Registrar usuario |
| | POST | `/api/login` | No | Iniciar sesión |
| | POST | `/api/logout` | Sí | Cerrar sesión |
| **RoomService** |
| | GET | `/api/rooms` | No | Listar habitaciones |
| | GET | `/api/rooms/{id}` | No | Ver una habitación |
| | POST | `/api/rooms` | Sí (Admin) | Crear habitación |
| | PUT | `/api/rooms/{id}` | Sí (Admin) | Actualizar habitación |
| | DELETE | `/api/rooms/{id}` | Sí (Admin) | Eliminar habitación |
| **ReservationService** |
| | GET | `/api/reservations` | Sí | Listar reservas |
| | POST | `/api/reservations` | Sí | Crear reserva |
| | GET | `/api/reservations/{id}` | Sí | Ver una reserva |
| | PUT | `/api/reservations/{id}` | Sí | Actualizar reserva |
| | DELETE | `/api/reservations/{id}` | Sí | Cancelar reserva |

---

## 🎯 Patrones y Mejores Prácticas Implementadas

### ✅ 1. Separación de Responsabilidades
- **Servicios**: Solo manejan comunicación HTTP
- **Componentes**: Solo manejan lógica de UI y presentación
- **Interceptores**: Solo modifican peticiones HTTP

### ✅ 2. Programación Reactiva con RxJS
- Uso de Observables para operaciones asíncronas
- Operador `tap()` para efectos secundarios
- BehaviorSubject para estado compartido

### ✅ 3. TypeScript Types
- Interfaces para todos los modelos de datos
- Type safety en servicios y componentes
- Autocompletado y detección de errores en tiempo de desarrollo

### ✅ 4. Manejo Centralizado de Autenticación
- Token agregado automáticamente por interceptor
- Estado de usuario centralizado en un servicio
- Persistencia en localStorage

### ✅ 5. Feedback al Usuario
- Estados de loading en todas las operaciones
- Notificaciones con MatSnackBar
- Manejo de errores con mensajes amigables

### ✅ 6. Seguridad
- Tokens en headers (no en URL)
- Contraseñas encriptadas en backend
- Validación de datos en frontend y backend
- Middleware de autenticación en rutas protegidas

---

**Documento creado para explicar técnicamente cómo se consumieron TODAS las APIs del proyecto.**

*Última actualización: Noviembre 2025*
