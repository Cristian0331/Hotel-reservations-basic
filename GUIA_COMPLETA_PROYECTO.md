# 🏨 Guía Completa del Sistema de Reservas de Hotel

## 📚 Índice
1. [¿Qué es este proyecto?](#qué-es-este-proyecto)
2. [Conceptos básicos que necesitas entender](#conceptos-básicos)
3. [Cómo funciona el Backend (La cocina del restaurante)](#backend-explicado)
4. [Cómo funciona el Frontend (El mesero y el menú)](#frontend-explicado)
5. [¿Cómo se comunican? (Las APIs)](#comunicación-apis)
6. [El flujo completo paso a paso](#flujo-completo)

---

## 🎯 ¿Qué es este proyecto?

Imagina que tienes un hotel. Necesitas un sistema para:
- Que las personas puedan ver las habitaciones disponibles
- Que puedan hacer reservas
- Que tú (el administrador) puedas agregar, editar o eliminar habitaciones
- Que las personas puedan registrarse e iniciar sesión

**Este proyecto hace exactamente eso**, pero de manera digital. Es como tener una recepcionista automática que trabaja 24/7.

---

## 🧠 Conceptos básicos que necesitas entender

### 1. **¿Qué es Frontend y Backend?**

Piensa en un restaurante:

**🎨 FRONTEND** = **El mesero y el menú**
- Es lo que TÚ ves y con lo que interactúas
- Es la página web bonita con botones, formularios y colores
- En nuestro proyecto usamos **Angular** (un framework de JavaScript)
- Se ejecuta en tu navegador (Chrome, Firefox, etc.)

**🍳 BACKEND** = **La cocina**
- Es donde se prepara todo pero no lo ves
- Aquí se guarda la información en la base de datos
- Se verifican permisos (¿eres administrador? ¿estás logueado?)
- En nuestro proyecto usamos **Laravel** (un framework de PHP)
- Se ejecuta en un servidor

### 2. **¿Qué es una API?**

**API** = **Application Programming Interface** (Interfaz de Programación de Aplicaciones)

Imagina que vas a un restaurante:
1. Tú (Frontend) le pides al mesero: "Quiero un café"
2. El mesero lleva tu pedido a la cocina (API)
3. La cocina prepara el café (Backend procesa)
4. El mesero te trae el café (API devuelve respuesta)

**La API es el mesero que lleva mensajes entre tú y la cocina.**

### 3. **¿Qué es una Base de Datos?**

Es como un archivador gigante donde guardas información organizada en tablas:

**Tabla "users" (usuarios):**
| id | nombre | email | contraseña |
|----|--------|-------|------------|
| 1 | María | maria@gmail.com | ******* |
| 2 | Juan | juan@gmail.com | ******* |

**Tabla "rooms" (habitaciones):**
| id | nombre | precio | capacidad |
|----|--------|--------|-----------|
| 1 | Suite presidencial | $200 | 4 personas |
| 2 | Habitación doble | $80 | 2 personas |

### 4. **¿Qué es un Token de Autenticación?**

Imagina que entras a un club exclusivo:
1. Muestras tu identificación en la entrada
2. Te dan una pulsera especial
3. Cada vez que quieres entrar a un área VIP, solo muestras la pulsera (no tu ID completa)

**El Token es esa pulsera digital.** Cada vez que haces una petición al servidor que requiere estar logueado, envías tu "pulsera digital" para demostrar quién eres.

---

## 🍳 Backend Explicado (Laravel - PHP)

### Estructura del Backend

```
backend/
├── app/
│   ├── Models/           ← Los "moldes" de nuestros datos
│   │   ├── User.php      ← Molde de un usuario
│   │   ├── Room.php      ← Molde de una habitación
│   │   └── Reservation.php ← Molde de una reserva
│   │
│   └── Http/
│       └── Controllers/  ← Los "chefs" que procesan peticiones
│           ├── AuthController.php       ← Chef de autenticación
│           ├── RoomController.php       ← Chef de habitaciones
│           └── ReservationController.php ← Chef de reservas
│
└── routes/
    └── api.php          ← El "menú" de servicios disponibles
```

---

### 📋 Paso 1: Los Modelos (Models)

Los **modelos** son como moldes de galletas. Definen la forma de nuestros datos.

#### **User.php - El Molde de Usuario**

```php
class User {
    // Campos que puede tener un usuario:
    - name        (nombre)
    - email       (correo electrónico)
    - password    (contraseña encriptada)
    - role        (rol: 'admin' o 'user')
    - phone       (teléfono, opcional)
}
```

**¿Qué hace?**
- Define cómo se ve un usuario en la base de datos
- Tiene características especiales:
  - `HasApiTokens`: Puede tener tokens de autenticación (pulseras digitales)
  - `$hidden`: Oculta la contraseña cuando envías datos (¡nunca enviamos contraseñas!)
  - `$fillable`: Lista de campos que se pueden llenar masivamente

#### **Room.php - El Molde de Habitación**

```php
class Room {
    // Campos que puede tener una habitación:
    - name          (nombre: "Suite presidencial")
    - description   (descripción)
    - price         (precio: 200)
    - capacity      (capacidad: 4 personas)
    - type          (tipo: "Suite", "Doble", etc.)
    - image_url     (URL de la imagen)
    - is_available  (¿está disponible? true/false)
    
    // Relación:
    - reservations() → Una habitación puede tener MUCHAS reservas
}
```

#### **Reservation.php - El Molde de Reserva**

```php
class Reservation {
    // Campos:
    - user_id      (¿Quién hizo la reserva?)
    - room_id      (¿Qué habitación reservó?)
    - check_in     (Fecha de entrada)
    - check_out    (Fecha de salida)
    - total_price  (Precio total)
    - status       (Estado: 'pending', 'confirmed', 'cancelled')
    
    // Relaciones:
    - user() → Pertenece a UN usuario
    - room() → Pertenece a UNA habitación
}
```

---

### 👨‍🍳 Paso 2: Los Controladores (Controllers)

Los **controladores** son los chefs que procesan las peticiones.

#### **AuthController.php - Chef de Autenticación**

**Tiene 3 funciones principales:**

##### 1. **register()** - Registrar nuevo usuario

**Explicación como si tuvieras 5 años:**
"Un nuevo cliente llega al hotel y llena un formulario con su nombre, email y contraseña. La recepcionista verifica que todo esté correcto y le da una tarjeta de huésped (token)."

**Paso a paso técnico:**
```
1. Recibe datos: nombre, email, contraseña, teléfono
2. VALIDA que:
   - El nombre no esté vacío
   - El email sea válido y no esté usado
   - La contraseña tenga al menos 8 caracteres
3. ENCRIPTA la contraseña (la convierte en código secreto)
4. GUARDA el usuario en la base de datos
5. CREA un token (pulsera digital)
6. DEVUELVE: el token y los datos del usuario
```

**¿Qué regresa?**
```json
{
  "access_token": "1|abcd1234...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "María",
    "email": "maria@gmail.com",
    "role": "user"
  }
}
```

##### 2. **login()** - Iniciar sesión

**Explicación simple:**
"Un cliente que ya está registrado llega al hotel. Muestra su email y contraseña. Si son correctos, le damos una nueva tarjeta de acceso."

**Paso a paso:**
```
1. Recibe: email y contraseña
2. BUSCA en la base de datos un usuario con ese email
3. VERIFICA si la contraseña es correcta
4. Si es correcta:
   - GENERA un nuevo token
   - DEVUELVE el token y datos del usuario
5. Si es incorrecta:
   - DEVUELVE error: "Credenciales inválidas"
```

##### 3. **logout()** - Cerrar sesión

**Explicación simple:**
"El cliente se va del hotel y devuelve su tarjeta de acceso. La destruimos para que ya no funcione."

**Paso a paso:**
```
1. Recibe la petición con el token
2. BUSCA ese token en la base de datos
3. ELIMINA el token
4. DEVUELVE mensaje: "Sesión cerrada exitosamente"
```

---

#### **RoomController.php - Chef de Habitaciones**

**Tiene 5 funciones (CRUD completo):**

##### 1. **index()** - Listar todas las habitaciones

```
1. BUSCA todas las habitaciones en la base de datos
2. DEVUELVE la lista completa
```

**Ejemplo de respuesta:**
```json
[
  {
    "id": 1,
    "name": "Suite Presidencial",
    "price": 200,
    "capacity": 4,
    "type": "Suite",
    "is_available": true
  },
  {
    "id": 2,
    "name": "Habitación Doble",
    "price": 80,
    "capacity": 2,
    "type": "Estándar",
    "is_available": true
  }
]
```

##### 2. **store()** - Crear nueva habitación (Solo Admin)

```
1. Recibe datos: nombre, descripción, precio, capacidad, tipo, imagen
2. VALIDA que todos los campos sean correctos
3. CREA la habitación en la base de datos
4. DEVUELVE la habitación creada
```

##### 3. **show(id)** - Ver una habitación específica

```
1. Recibe el ID de la habitación (ejemplo: 5)
2. BUSCA esa habitación en la base de datos
3. Si existe: DEVUELVE sus datos
4. Si NO existe: DEVUELVE error 404 "No encontrado"
```

##### 4. **update(id)** - Actualizar habitación (Solo Admin)

```
1. Recibe el ID de la habitación y los datos nuevos
2. BUSCA la habitación
3. ACTUALIZA los campos que cambiaron
4. GUARDA en la base de datos
5. DEVUELVE la habitación actualizada
```

##### 5. **destroy(id)** - Eliminar habitación (Solo Admin)

```
1. Recibe el ID de la habitación
2. BUSCA la habitación
3. ELIMINA de la base de datos
4. DEVUELVE respuesta vacía (código 204)
```

---

#### **ReservationController.php - Chef de Reservas**

##### 1. **index()** - Listar reservas

**Aquí hay lógica especial:**

```
1. Identifica quién hace la petición (usando el token)
2. Si es ADMIN:
   - DEVUELVE TODAS las reservas de todos los usuarios
3. Si es USER normal:
   - DEVUELVE solo SUS reservas
```

**¿Por qué?** Porque no queremos que un usuario vea las reservas de otros usuarios. ¡Privacidad!

##### 2. **store()** - Crear reserva

```
1. Recibe:
   - room_id (¿Qué habitación?)
   - check_in (Fecha de entrada)
   - check_out (Fecha de salida)
   - total_price (Precio total)
   
2. VALIDA:
   - Que la habitación exista
   - Que check_out sea DESPUÉS de check_in
   - Que el precio sea un número válido
   
3. IDENTIFICA al usuario (del token)

4. CREA la reserva en la base de datos con:
   - user_id: El usuario que hace la petición
   - room_id, check_in, check_out, total_price
   - status: 'pending' (pendiente)
   
5. DEVUELVE la reserva creada
```

---

### 🗺️ Paso 3: Las Rutas (api.php)

Las rutas son como el **GPS del sistema**. Le dicen al backend: "Si recibes esta petición, envíala a este controlador".

```php
// RUTAS PÚBLICAS (cualquiera puede usarlas)
POST /api/register          → AuthController@register
POST /api/login             → AuthController@login
GET  /api/rooms             → RoomController@index
GET  /api/rooms/5           → RoomController@show (habitación #5)

// RUTAS PROTEGIDAS (necesitas estar logueado)
// middleware('auth:sanctum') = "Verifica la pulsera digital"

POST   /api/logout          → AuthController@logout
GET    /api/user            → Devuelve datos del usuario logueado
POST   /api/rooms           → RoomController@store
PUT    /api/rooms/5         → RoomController@update
DELETE /api/rooms/5         → RoomController@destroy
GET    /api/reservations    → ReservationController@index
POST   /api/reservations    → ReservationController@store
```

**Ejemplo de cómo funciona:**

```
Usuario hace: GET /api/rooms
     ↓
Laravel recibe la petición
     ↓
Busca en api.php: "GET /api/rooms"
     ↓
Encuentra: RoomController@index
     ↓
Ejecuta la función index()
     ↓
Devuelve lista de habitaciones
```

---

## 🎨 Frontend Explicado (Angular - TypeScript)

### Estructura del Frontend

```
frontend/
└── src/
    └── app/
        ├── services/           ← Los "mensajeros" que hablan con el backend
        │   ├── auth.ts         ← Mensajero de autenticación
        │   ├── room.ts         ← Mensajero de habitaciones
        │   └── reservation.ts  ← Mensajero de reservas
        │
        ├── interceptors/       ← "Guardias" que modifican peticiones
        │   └── auth-interceptor.ts ← Agrega el token a TODAS las peticiones
        │
        └── components/         ← Las "pantallas" que ves
            ├── login/          ← Pantalla de inicio de sesión
            ├── register/       ← Pantalla de registro
            ├── rooms/          ← Pantalla de habitaciones
            ├── reservations/   ← Pantalla de reservas
            └── admin-dashboard/ ← Panel de administración
```

---

### 📨 Paso 1: Los Servicios (Services)

Los servicios son **mensajeros especializados**. Cada uno sabe cómo hablar con una parte específica del backend.

#### **auth.ts - Servicio de Autenticación**

**¿Qué hace?**
Es el mensajero encargado de todo lo relacionado con usuarios: registrarse, iniciar sesión y cerrar sesión.

**Variables importantes:**
```typescript
apiUrl = 'http://localhost:8000/api'  // Dirección del backend
currentUserSubject                     // "Caja mágica" con datos del usuario
currentUser$                           // "Ventanita" para ver qué hay en la caja
```

**Funciones principales:**

##### 1. **register(data)** - Registrar usuario

**Paso a paso:**
```
1. Recibe datos del formulario: nombre, email, contraseña, teléfono
2. ENVÍA petición POST a: http://localhost:8000/api/register
3. ESPERA respuesta del backend
4. Cuando llega la respuesta:
   - GUARDA el token en localStorage (memoria del navegador)
   - GUARDA los datos del usuario en localStorage
   - ACTUALIZA currentUserSubject (para que toda la app sepa que hay un usuario)
5. DEVUELVE la respuesta al componente
```

##### 2. **login(email, password)** - Iniciar sesión

```
1. Recibe email y contraseña
2. ENVÍA petición POST a: http://localhost:8000/api/login
3. ESPERA respuesta
4. Si es exitosa:
   - GUARDA token en localStorage
   - GUARDA usuario en localStorage
   - ACTUALIZA currentUserSubject
5. DEVUELVE la respuesta
```

##### 3. **logout()** - Cerrar sesión

```
1. ENVÍA petición POST a: http://localhost:8000/api/logout
2. Sin importar la respuesta:
   - ELIMINA token de localStorage
   - ELIMINA usuario de localStorage
   - ACTUALIZA currentUserSubject a null (nadie logueado)
```

##### 4. **Funciones auxiliares:**

```typescript
getToken()        → Obtiene el token guardado
getCurrentUser()  → Obtiene datos del usuario actual
isLoggedIn()      → ¿Hay alguien logueado? (true/false)
isAdmin()         → ¿El usuario es admin? (true/false)
```

---

#### **room.ts - Servicio de Habitaciones**

**Funciones:**

##### 1. **getRooms()** - Obtener todas las habitaciones

```
1. ENVÍA: GET http://127.0.0.1:8000/api/rooms
2. ESPERA: Lista de habitaciones
3. DEVUELVE: Observable con las habitaciones
```

**Nota:** `Observable` es como una "caja de promesas". No es un valor inmediato, sino una promesa de que llegará un valor.

##### 2. **getRoom(id)** - Obtener una habitación

```
1. ENVÍA: GET http://127.0.0.1:8000/api/rooms/5
2. ESPERA: Datos de la habitación #5
3. DEVUELVE: Observable con la habitación
```

##### 3. **createRoom(room)** - Crear habitación

```
1. Recibe objeto con datos de la habitación
2. ENVÍA: POST http://127.0.0.1:8000/api/rooms
        Con el token en el header (lo agrega el interceptor)
3. ESPERA: La habitación creada
4. DEVUELVE: Observable con la habitación
```

##### 4. **updateRoom(id, room)** - Actualizar

```
ENVÍA: PUT http://127.0.0.1:8000/api/rooms/5
       Con los datos nuevos
```

##### 5. **deleteRoom(id)** - Eliminar

```
ENVÍA: DELETE http://127.0.0.1:8000/api/rooms/5
```

---

#### **reservation.ts - Servicio de Reservas**

Similar a RoomService pero para reservas:

```typescript
getReservations()              → GET /api/reservations
getReservation(id)             → GET /api/reservations/5
createReservation(reservation) → POST /api/reservations
updateReservation(id, data)    → PUT /api/reservations/5
deleteReservation(id)          → DELETE /api/reservations/5
```

---

### 🛡️ Paso 2: El Interceptor (auth-interceptor.ts)

**¿Qué es?**
Es un **guardia automático** que intercepta TODAS las peticiones HTTP antes de enviarlas.

**Analogía:**
Imagina que cada vez que envías una carta, un asistente verifica si tienes tu "pulsera VIP" y la adjunta automáticamente a la carta.

**Código explicado:**

```typescript
export const authInterceptor = (req, next) => {
  // 1. Buscar el token en localStorage
  const token = localStorage.getItem('token');
  
  // 2. Si hay token
  if (token) {
    // 3. Clona la petición original
    const cloned = req.clone({
      // 4. Agrega el header de autorización
      headers: req.headers.set('Authorization', `Bearer ${token}`)
    });
    // 5. Envía la petición modificada
    return next(cloned);
  }
  
  // 6. Si no hay token, envía la petición original
  return next(req);
};
```

**¿Por qué es importante?**
Sin esto, tendríamos que agregar manualmente el token en CADA petición. Esto lo hace automáticamente.

**Ejemplo de petición antes y después:**

**ANTES del interceptor:**
```http
GET /api/reservations
(Sin headers de autorización) ❌
```

**DESPUÉS del interceptor:**
```http
GET /api/reservations
Authorization: Bearer 1|abcd1234... ✅
```

---

### 🖼️ Paso 3: Los Componentes (Components)

Los componentes son las **pantallas** que ves e interactúas.

#### **login.ts - Componente de Login**

**Variables:**
```typescript
loginForm     → El formulario con campos: email, password
hidePassword  → ¿Ocultar contraseña? (true/false)
loading       → ¿Está cargando? (muestra el spinner)
```

**Función principal: onSubmit()**

```
1. El usuario llena el formulario y da click en "Iniciar Sesión"

2. VALIDA el formulario:
   - ¿El email es válido?
   - ¿La contraseña tiene al menos 6 caracteres?
   
3. Si NO es válido:
   - Marca los campos con error (se ponen rojos)
   - Muestra mensaje: "Completa todos los campos"
   - TERMINA aquí
   
4. Si es válido:
   - Cambia loading a true (muestra spinner)
   - Llama a authService.login(email, password)
   
5. ESPERA respuesta:
   
   A) Si es EXITOSA (next):
      - Limpia el formulario
      - Muestra notificación: "¡Inicio de sesión exitoso!"
      - Espera 500ms
      - Navega a la página principal (/)
      
   B) Si es ERROR:
      - Muestra mensaje: "Credenciales inválidas"
      - Imprime el error en consola
      - Cambia loading a false (quita spinner)
```

---

#### **rooms.ts - Componente de Habitaciones**

**Variables:**
```typescript
rooms    → Lista de habitaciones (inicialmente vacía [])
loading  → ¿Está cargando?
```

**Función: loadRooms()**

```
1. Cambia loading a true (muestra spinner)

2. Llama a roomService.getRooms()

3. ESPERA respuesta:
   
   A) Si es exitosa:
      - Guarda las habitaciones en la variable rooms
      - Cambia loading a false
      - El HTML automáticamente muestra las cards
      
   B) Si hay error:
      - Cambia loading a false
      - Muestra mensaje: "Error al cargar habitaciones..."
      - El usuario ve el mensaje de error
```

**Función: reserveRoom(room)**

```
1. Usuario hace click en "Reservar" en una habitación

2. VERIFICA si el usuario está logueado:
   - Si NO: 
     - Muestra: "Debes iniciar sesión para reservar"
     - Redirige a /login
   - Si SÍ:
     - Navega a /reservations
     - Pasa los datos de la habitación seleccionada
```

**HTML simplificado:**

```html
<!-- Si está cargando, muestra spinner -->
<spinner *ngIf="loading"></spinner>

<!-- Si NO está cargando, muestra las habitaciones -->
<div *ngIf="!loading">
  <!-- Por cada habitación en rooms, crea una card -->
  <card *ngFor="let room of rooms">
    <h3>{{ room.name }}</h3>
    <p>Precio: ${{ room.price }}</p>
    <p>Capacidad: {{ room.capacity }} personas</p>
    <button (click)="reserveRoom(room)">Reservar</button>
  </card>
</div>
```

---

#### **admin-dashboard.ts - Panel de Administración**

**Funciones:**

##### 1. **loadData()** - Cargar datos

```
Hace 2 peticiones en paralelo:
1. roomService.getRooms()     → Obtiene habitaciones
2. reservationService.getReservations() → Obtiene reservas

Guarda ambas en variables para mostrarlas
```

##### 2. **openCreateDialog()** - Crear habitación

```
1. Abre un diálogo (ventana emergente) con un formulario

2. Espera a que el admin llene los campos:
   - Nombre
   - Descripción
   - Precio
   - Capacidad
   - Tipo
   - URL de imagen
   
3. Cuando el admin da "Guardar":
   - Llama a roomService.createRoom(datos)
   - Si es exitoso:
     - Muestra: "✅ Habitación creada exitosamente"
     - Recarga los datos (loadData())
   - Si hay error:
     - Muestra: "❌ Error al crear habitación"
```

##### 3. **openEditDialog(room)** - Editar

```
Similar a create, pero:
1. El formulario viene PRE-LLENADO con los datos actuales
2. Llama a roomService.updateRoom(id, datos)
```

##### 4. **deleteRoom(id)** - Eliminar

```
1. Muestra confirmación: "⚠️ ¿Estás seguro?"

2. Si el admin confirma:
   - Llama a roomService.deleteRoom(id)
   - Si es exitoso:
     - Muestra: "✅ Habitación eliminada"
     - Recarga los datos
   - Si hay error:
     - Muestra: "❌ Error al eliminar"
```

---

#### **reservations.ts - Componente de Reservas**

**Variables:**
```typescript
reservationForm  → Formulario con: room_id, check_in, check_out, total_price
reservations     → Lista de reservas del usuario
selectedRoom     → Habitación seleccionada (viene de rooms)
loading          → Estado de carga
```

**Constructor (se ejecuta al crear el componente):**

```
1. Inicializa el formulario vacío

2. Verifica si viene de la pantalla de rooms:
   - Si hay una habitación seleccionada:
     - Pre-llena room_id y total_price
```

**Función: onSubmit()**

```
1. Usuario llena fechas de check-in y check-out

2. VALIDA el formulario

3. Si es válido:
   - Cambia loading a true
   - Llama a reservationService.createReservation(datos)
   
4. ESPERA respuesta:
   - Si es exitosa:
     - Muestra: "¡Reserva creada exitosamente!"
     - Recarga la lista de reservas
     - Limpia el formulario
   - Si hay error:
     - Muestra: "Error al crear reserva"
```

---

## 🔗 Comunicación APIs - El Flujo Completo

Ahora veamos TODO el proceso desde que un usuario hace click hasta que ve el resultado.

### Ejemplo 1: Usuario Inicia Sesión

#### Paso a paso COMPLETO:

**1. Usuario llena el formulario**
```
Email: maria@gmail.com
Contraseña: micontraseña123
```

**2. Usuario hace click en "Iniciar Sesión"**

**3. En login.ts → onSubmit():**
```typescript
// Se ejecuta esta línea:
this.authService.login('maria@gmail.com', 'micontraseña123')
```

**4. En auth.ts → login():**
```typescript
// Se ejecuta:
this.http.post('http://localhost:8000/api/login', {
  email: 'maria@gmail.com',
  password: 'micontraseña123'
})
```

**5. El Auth Interceptor intercepta:**
```typescript
// Busca token (no hay porque no está logueado)
// Envía la petición SIN modificar
```

**6. La petición viaja por internet:**
```
Frontend (localhost:4200) → Backend (localhost:8000)
```

**7. Laravel recibe la petición:**
```
POST /api/login
Body: {
  "email": "maria@gmail.com",
  "password": "micontraseña123"
}
```

**8. Laravel busca en api.php:**
```php
POST /api/login → AuthController@login
```

**9. En AuthController.php → login():**
```php
// 1. Intenta autenticar
Auth::attempt(['email' => 'maria@gmail.com', 'password' => 'micontraseña123'])

// 2. Busca en la base de datos:
SELECT * FROM users WHERE email = 'maria@gmail.com'

// 3. Compara contraseñas (encriptadas)
if (Hash::check('micontraseña123', $user->password)) {
    // ✅ Correcto
    
    // 4. Genera token
    $token = $user->createToken('auth_token')->plainTextToken;
    // Token generado: "1|Xy89AbCdEf..."
    
    // 5. Devuelve respuesta
    return json({
        "access_token": "1|Xy89AbCdEf...",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "María",
            "email": "maria@gmail.com",
            "role": "user"
        }
    });
}
```

**10. La respuesta viaja de vuelta:**
```
Backend → Frontend
```

**11. Angular recibe la respuesta en auth.ts:**
```typescript
// El operador tap() ejecuta handleAuth()
handleAuth(response) {
  // Guarda en localStorage:
  localStorage.setItem('token', '1|Xy89AbCdEf...')
  localStorage.setItem('user', '{"id":1,"name":"María",...}')
  
  // Actualiza el BehaviorSubject:
  this.currentUserSubject.next(user)
}
```

**12. login.ts recibe la respuesta:**
```typescript
next: (response) => {
  // Limpia el formulario
  this.loginForm.reset();
  
  // Muestra notificación
  snackBar.open('¡Inicio de sesión exitoso!')
  
  // Espera 500ms
  setTimeout(() => {
    // Navega a la página principal
    router.navigate(['/'])
  }, 500)
}
```

**13. El usuario ve:**
```
✅ Notificación verde: "¡Inicio de sesión exitoso! Redirigiendo..."
→ Automáticamente va a la página principal
→ Ahora puede ver su nombre en el navbar
→ Ahora puede hacer reservas
```

---

### Ejemplo 2: Administrador Crea una Habitación

#### Paso a paso:

**1. Admin ya está logueado**
```
localStorage tiene:
- token: "2|AdMin123Token..."
- user: { id: 2, name: "Admin", role: "admin" }
```

**2. Admin va a /admin y hace click en "Nueva Habitación"**

**3. Se abre un diálogo con formulario:**
```
Nombre: Suite Presidencial VIP
Descripción: Habitación de lujo con vista al mar
Precio: 350
Capacidad: 4
Tipo: Suite
```

**4. Admin da click en "Guardar"**

**5. En admin-dashboard.ts:**
```typescript
this.roomService.createRoom({
  name: 'Suite Presidencial VIP',
  description: 'Habitación de lujo...',
  price: 350,
  capacity: 4,
  type: 'Suite',
  is_available: true
})
```

**6. En room.ts → createRoom():**
```typescript
this.http.post('http://127.0.0.1:8000/api/rooms', {
  name: 'Suite Presidencial VIP',
  description: 'Habitación de lujo...',
  price: 350,
  capacity: 4,
  type: 'Suite',
  is_available: true
})
```

**7. Auth Interceptor intercepta:**
```typescript
// Busca token en localStorage
token = "2|AdMin123Token..."

// CLONA la petición y agrega header
headers.set('Authorization', 'Bearer 2|AdMin123Token...')

// Envía la petición MODIFICADA
```

**8. Laravel recibe:**
```
POST /api/rooms
Headers:
  Authorization: Bearer 2|AdMin123Token...
Body:
  { name: "Suite Presidencial VIP", ... }
```

**9. Laravel ve que la ruta está protegida:**
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/rooms', [RoomController::class, 'store']);
});
```

**10. Middleware 'auth:sanctum' verifica:**
```php
// 1. Extrae el token del header
$token = "2|AdMin123Token..."

// 2. Busca en la tabla personal_access_tokens:
SELECT * FROM personal_access_tokens 
WHERE token = hash('2|AdMin123Token...')

// 3. Si existe y no ha expirado:
//    ✅ Permite continuar
//    Identifica al usuario (id: 2)
```

**11. Se ejecuta RoomController@store:**
```php
// 1. Valida los datos
$validated = [
  'name' => 'Suite Presidencial VIP',  // ✅ String válido
  'price' => 350,                       // ✅ Número válido
  'capacity' => 4,                      // ✅ Entero válido
  ...
];

// 2. Crea la habitación en la DB
INSERT INTO rooms (name, description, price, capacity, type, is_available)
VALUES ('Suite Presidencial VIP', 'Habitación de lujo...', 350, 4, 'Suite', 1)

// 3. Obtiene la habitación recién creada (con su ID)
$room = { id: 15, name: "Suite Presidencial VIP", ... }

// 4. Devuelve respuesta
return json($room, 201)  // 201 = Created
```

**12. La respuesta vuelve a Angular:**
```typescript
next: () => {
  // Muestra notificación
  snackBar.open('✅ Habitación creada exitosamente')
  
  // Recarga los datos
  this.loadData()  // Hace GET /api/rooms para actualizar la tabla
}
```

**13. El admin ve:**
```
✅ Notificación: "Habitación creada exitosamente"
→ La nueva habitación aparece en la tabla
→ Los usuarios ya pueden verla y reservarla
```

---

### Ejemplo 3: Usuario Hace una Reserva

**1. Usuario está en /rooms viendo habitaciones**

**2. Ve una habitación que le gusta:**
```
Suite Presidencial VIP
$350 por noche
Capacidad: 4 personas
[Botón: Reservar]
```

**3. Click en "Reservar"**

**4. En rooms.ts → reserveRoom():**
```typescript
// Verifica si está logueado
if (!this.authService.isLoggedIn()) {
  // NO está logueado
  snackBar.open('Debes iniciar sesión para reservar')
  router.navigate(['/login'])
  return;  // TERMINA aquí
}

// SÍ está logueado
router.navigate(['/reservations'], { 
  state: { room: selectedRoom }  // Pasa la habitación seleccionada
})
```

**5. Va a /reservations**

**6. El formulario viene pre-llenado:**
```
Habitación: Suite Presidencial VIP (pre-seleccionada)
Precio: $350 (pre-llenado)
Fecha entrada: [Usuario debe llenar]
Fecha salida: [Usuario debe llenar]
```

**7. Usuario llena las fechas:**
```
Entrada: 2025-12-25
Salida: 2025-12-28
```

**8. Click en "Confirmar Reserva"**

**9. En reservations.ts → onSubmit():**
```typescript
this.reservationService.createReservation({
  room_id: 15,
  check_in: '2025-12-25',
  check_out: '2025-12-28',
  total_price: 1050  // 3 noches * $350
})
```

**10. Petición HTTP:**
```
POST http://localhost:8000/api/reservations
Headers:
  Authorization: Bearer 1|Xy89AbCdEf...  (agregado por interceptor)
Body:
  {
    "room_id": 15,
    "check_in": "2025-12-25",
    "check_out": "2025-12-28",
    "total_price": 1050
  }
```

**11. Laravel middleware verifica token** ✅

**12. ReservationController@store:**
```php
// 1. Valida
$validated = [
  'room_id' => 15,                    // ✅ Habitación existe
  'check_in' => '2025-12-25',         // ✅ Fecha válida
  'check_out' => '2025-12-28',        // ✅ Después de check_in
  'total_price' => 1050,              // ✅ Número válido
];

// 2. Obtiene el usuario autenticado (del token)
$user = request()->user();  // { id: 1, name: "María" }

// 3. Crea la reserva
INSERT INTO reservations (user_id, room_id, check_in, check_out, total_price, status)
VALUES (1, 15, '2025-12-25', '2025-12-28', 1050, 'pending')

// 4. Devuelve
return json({
  id: 42,
  user_id: 1,
  room_id: 15,
  check_in: '2025-12-25',
  check_out: '2025-12-28',
  total_price: 1050,
  status: 'pending'
}, 201)
```

**13. Angular muestra:**
```
✅ "¡Reserva creada exitosamente!"
→ La reserva aparece en la lista
```

---

## 🎯 Flujo Visual Completo

```
┌─────────────────────────────────────────────────────────────┐
│                    NAVEGADOR (Frontend)                     │
│                                                             │
│  1. Usuario interactúa con la página                       │
│     (Llena formularios, hace clicks)                       │
│         ↓                                                   │
│  2. Componente Angular ejecuta función                     │
│         ↓                                                   │
│  3. Llama a un Servicio                                    │
│         ↓                                                   │
│  4. Servicio hace petición HTTP                            │
│         ↓                                                   │
│  5. Auth Interceptor agrega token                          │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ Internet (HTTP Request)
                  │
┌─────────────────▼───────────────────────────────────────────┐
│                   SERVIDOR (Backend)                        │
│                                                             │
│  6. Laravel recibe la petición                             │
│         ↓                                                   │
│  7. Busca la ruta en api.php                               │
│         ↓                                                   │
│  8. Si está protegida, verifica token (middleware)         │
│         ↓                                                   │
│  9. Ejecuta el Controlador correspondiente                 │
│         ↓                                                   │
│  10. Controlador usa el Modelo                             │
│         ↓                                                   │
│  11. Modelo interactúa con la Base de Datos                │
│         ↓                                                   │
│  12. Devuelve respuesta JSON                               │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ Internet (HTTP Response)
                  │
┌─────────────────▼───────────────────────────────────────────┐
│                    NAVEGADOR (Frontend)                     │
│                                                             │
│  13. Servicio recibe respuesta                             │
│         ↓                                                   │
│  14. Componente procesa respuesta                          │
│         ↓                                                   │
│  15. Actualiza la vista (HTML)                             │
│         ↓                                                   │
│  16. Usuario ve el resultado                               │
│      (Notificación, datos actualizados, etc.)              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔑 Conceptos Clave - Resumen

### 1. **Autenticación con Token**

```
Login → Backend genera token → Frontend lo guarda → 
Interceptor lo agrega automáticamente → Backend lo verifica
```

### 2. **CRUD (Create, Read, Update, Delete)**

```
Create (POST)   → Crear nuevo registro
Read (GET)      → Leer/Obtener registros
Update (PUT)    → Actualizar registro existente
Delete (DELETE) → Eliminar registro
```

### 3. **Observables (RxJS)**

Son "promesas mejoradas". No dan un valor inmediato, sino que esperas a que llegue.

```typescript
// Sin Observable (síncrono)
let rooms = getRooms();  // ❌ No funciona así

// Con Observable (asíncrono)
getRooms().subscribe(rooms => {
  // Aquí ya tienes las habitaciones
  console.log(rooms);
});
```

### 4. **Middleware**

Son "guardias" que revisan peticiones antes de ejecutar la lógica principal.

```
Petición → Middleware verifica token → Si es válido: Continúa
                                     → Si NO: Devuelve error 401
```

### 5. **Relaciones en Base de Datos**

```
User (1) ←→ (Muchas) Reservations
Room (1) ←→ (Muchas) Reservations

Una reserva PERTENECE a un usuario y a una habitación
```

---

## 📋 Glosario de Términos

| Término | Explicación Simple |
|---------|-------------------|
| **API** | El "mesero" que lleva mensajes entre frontend y backend |
| **Token** | "Pulsera digital" que demuestra que estás logueado |
| **Middleware** | "Guardia" que verifica cosas antes de ejecutar código |
| **Controller** | "Chef" que procesa las peticiones |
| **Model** | "Molde" que define la estructura de los datos |
| **Service** | "Mensajero" del frontend que habla con el backend |
| **Component** | "Pantalla" que el usuario ve e interactúa |
| **Observable** | "Promesa mejorada" de un valor que llegará |
| **Interceptor** | "Asistente" que modifica peticiones automáticamente |
| **CRUD** | Create, Read, Update, Delete (Crear, Leer, Actualizar, Eliminar) |
| **Route** | "Dirección" o "camino" para llegar a una función |
| **HTTP Methods** | GET (obtener), POST (crear), PUT (actualizar), DELETE (eliminar) |
| **JSON** | Formato de datos (como una "carta" estructurada) |
| **Headers** | "Sobre" de la carta HTTP con información adicional |
| **Body** | "Contenido" de la carta HTTP con los datos |
| **Status Code** | Número que indica el resultado: 200 (OK), 401 (No autorizado), 404 (No encontrado) |

---

## 🎓 Conclusión

Este sistema de reservas de hotel es como un restaurante bien organizado:

- **Frontend (Angular)**: El mesero que toma tu orden y te trae la comida
- **Backend (Laravel)**: La cocina que prepara todo
- **API**: El sistema de comunicación entre mesero y cocina
- **Base de Datos**: El almacén donde guardas todos los ingredientes
- **Token**: Tu pulsera VIP que te identifica

Todo trabaja en conjunto para dar una experiencia fluida al usuario, donde puede:
- ✅ Registrarse e iniciar sesión de forma segura
- ✅ Ver habitaciones disponibles
- ✅ Hacer reservas fácilmente
- ✅ Los administradores pueden gestionar todo desde un panel

**¡Y todo esto sucede en segundos, automáticamente!** 🚀

---

## 📚 Recursos para Seguir Aprendiendo

1. **Angular**: https://angular.io/docs
2. **Laravel**: https://laravel.com/docs
3. **APIs REST**: https://restfulapi.net/
4. **HTTP**: https://developer.mozilla.org/es/docs/Web/HTTP

---

*Documento creado para explicar el sistema de forma comprensible para cualquier persona, sin importar su nivel técnico.* 💡
