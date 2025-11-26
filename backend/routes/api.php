<?php

/*
 * ========================================
 * ARCHIVO DE RUTAS API
 * ========================================
 * 
 * Este archivo es como el "MAPA o DIRECTORIO" del backend.
 * Define TODAS las rutas (URLs) disponibles y a dónde van.
 * 
 * CONCEPTO CLAVE:
 * Cuando el frontend hace una petición a una URL, Laravel busca aquí
 * para saber qué controlador y función debe ejecutar.
 * 
 * EJEMPLO:
 * Frontend hace: GET http://localhost:8000/api/rooms
 * Laravel busca aquí y encuentra: Route::get('/rooms', [RoomController::class, 'index'])
 * Entonces ejecuta: RoomController → función index()
 * 
 * IMPORTANTE:
 * - Todas las rutas aquí tienen el prefijo "/api" automáticamente
 * - Las rutas públicas están arriba (no requieren login)
 * - Las rutas protegidas están dentro de Route::middleware('auth:sanctum')
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importar los controladores que vamos a usar
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;

/*
 * ========================================
 * RUTAS PÚBLICAS (No requieren autenticación)
 * ========================================
 * 
 * Estas rutas pueden ser accedidas por CUALQUIER persona,
 * incluso si no tienen cuenta o no han iniciado sesión.
 */

// REGISTRO DE USUARIOS
// POST /api/register
// Permite crear una nueva cuenta de usuario
// Ejecuta: AuthController → función register()
Route::post('/register', [AuthController::class, 'register']);

// INICIO DE SESIÓN
// POST /api/login
// Verifica email y contraseña, devuelve un token si son correctos
// Ejecuta: AuthController → función login()
Route::post('/login', [AuthController::class, 'login']);

// ========================================
// RUTAS PÚBLICAS DE HABITACIONES
// ========================================

// LISTAR TODAS LAS HABITACIONES
// GET /api/rooms
// Cualquier persona puede ver el catálogo de habitaciones
// Ejecuta: RoomController → función index()
Route::get('/rooms', [RoomController::class, 'index']);

// VER UNA HABITACIÓN ESPECÍFICA
// GET /api/rooms/{id}
// Ejemplo: GET /api/rooms/5
// Ver detalles de una habitación en particular
// Ejecuta: RoomController → función show(id)
Route::get('/rooms/{id}', [RoomController::class, 'show']);

/*
 * ========================================
 * RUTAS PROTEGIDAS (Requieren autenticación)
 * ========================================
 * 
 * Todas las rutas dentro de Route::middleware('auth:sanctum')->group()
 * están PROTEGIDAS. Solo usuarios con un token válido pueden accederlas.
 * 
 * CÓMO FUNCIONA:
 * 1. El frontend envía el token en el header: Authorization: Bearer {token}
 * 2. El middleware 'auth:sanctum' verifica que el token sea válido
 * 3. Si es válido: permite continuar
 * 4. Si NO es válido o no existe: devuelve error 401 (Unauthorized)
 */
Route::middleware('auth:sanctum')->group(function () {
    
    // ========================================
    // RUTAS DE AUTENTICACIÓN
    // ========================================
    
    // CERRAR SESIÓN
    // POST /api/logout
    // Destruye el token actual del usuario
    // Ejecuta: AuthController → función logout()
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // OBTENER USUARIO ACTUAL
    // GET /api/user
    // Devuelve los datos del usuario que está logueado (del token)
    // Esta es una función inline (directamente en la ruta)
    Route::get('/user', function (Request $request) {
        return $request->user();  // Devuelve el usuario autenticado
    });

    // ========================================
    // RUTAS DE HABITACIONES (ADMIN)
    // ========================================
    // NOTA: Idealmente estas deberían tener otro middleware para verificar
    // que el usuario sea admin, pero por simplicidad están solo con auth
    
    // CREAR HABITACIÓN
    // POST /api/rooms
    // Solo admins deberían poder crear habitaciones
    // Ejecuta: RoomController → función store()
    Route::post('/rooms', [RoomController::class, 'store']);
    
    // ACTUALIZAR HABITACIÓN
    // PUT /api/rooms/{id}
    // Ejemplo: PUT /api/rooms/5
    // Modificar una habitación existente
    // Ejecuta: RoomController → función update(id)
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    
    // ELIMINAR HABITACIÓN
    // DELETE /api/rooms/{id}
    // Ejemplo: DELETE /api/rooms/5
    // Eliminar una habitación permanentemente
    // Ejecuta: RoomController → función destroy(id)
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);

    // ========================================
    // RUTAS DE RESERVAS (COMPLETAS - CRUD)
    // ========================================
    
    // apiResource() es un ATAJO que crea automáticamente 5 rutas:
    // GET    /api/reservations          → index()   (listar todas)
    // POST   /api/reservations          → store()   (crear nueva)
    // GET    /api/reservations/{id}     → show(id)  (ver una)
    // PUT    /api/reservations/{id}     → update(id)(actualizar)
    // DELETE /api/reservations/{id}     → destroy(id)(eliminar)
    //
    // Es equivalente a escribir las 5 rutas manualmente
    Route::apiResource('reservations', ReservationController::class);
});

/*
 * ========================================
 * RESUMEN DE RUTAS DISPONIBLES
 * ========================================
 * 
 * PÚBLICAS:
 * POST   /api/register          → Registrarse
 * POST   /api/login             → Iniciar sesión
 * GET    /api/rooms             → Ver todas las habitaciones
 * GET    /api/rooms/{id}        → Ver una habitación
 * 
 * PROTEGIDAS (necesitan token):
 * POST   /api/logout            → Cerrar sesión
 * GET    /api/user              → Obtener datos del usuario actual
 * POST   /api/rooms             → Crear habitación (admin)
 * PUT    /api/rooms/{id}        → Actualizar habitación (admin)
 * DELETE /api/rooms/{id}        → Eliminar habitación (admin)
 * GET    /api/reservations      → Ver mis reservas (o todas si eres admin)
 * POST   /api/reservations      → Crear reserva
 * GET    /api/reservations/{id} → Ver una reserva
 * PUT    /api/reservations/{id} → Actualizar estado de reserva
 * DELETE /api/reservations/{id} → Cancelar reserva
 */

