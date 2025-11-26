<?php

/*
 * ========================================
 * CONTROLADOR DE RESERVAS (RESERVATIONS)
 * ========================================
 * 
 * Este controlador maneja todo el sistema de reservas del hotel.
 * Es como el "sistema de control de reservaciones".
 * 
 * CARACTERÍSTICAS IMPORTANTES:
 * - Usuarios normales solo pueden ver SUS propias reservas
 * - Administradores pueden ver TODAS las reservas
 * - Todas las rutas están protegidas (requieren autenticación)
 * - Automáticamente vincula las reservas con el usuario logueado
 * 
 * FUNCIONALIDADES:
 * - Listar reservas (con filtro por rol)
 * - Crear nueva reserva
 * - Ver una reserva específica
 * - Actualizar estado de reserva
 * - Cancelar/eliminar reserva
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * ========================================
     * LISTAR RESERVAS (CON LÓGICA DE PERMISOS)
     * ========================================
     * 
     * Esta función tiene LÓGICA ESPECIAL según el rol del usuario:
     * 
     * Si eres ADMIN:
     *   - Ves TODAS las reservas de todos los usuarios
     *   - Incluye información del usuario y la habitación
     * 
     * Si eres USER normal:
     *   - Solo ves TUS propias reservas
     *   - Incluye información de la habitación reservada
     * 
     * RUTA: GET /api/reservations
     * PROTEGIDA: Requiere token de autenticación
     * 
     * DEVUELVE:
     * Array de reservas con información relacionada
     */
    public function index(Request $request)
    {
        // PASO 1: Obtener el usuario que hace la petición
        // $request->user() obtiene el usuario desde el token
        $user = $request->user();
        
        // PASO 2: Verificar el rol del usuario
        if ($user->role === 'admin') {
            // Si es ADMIN: Devuelve TODAS las reservas
            // with(['user', 'room']) carga también los datos del usuario y habitación
            // Esto se llama "Eager Loading" y evita múltiples consultas a la BD
            return \App\Models\Reservation::with(['user', 'room'])->get();
        }
        
        // Si es USER normal: Solo devuelve SUS reservas
        // where('user_id', $user->id) filtra por el ID del usuario
        // with(['room']) solo carga info de la habitación (no necesita ver otros usuarios)
        return \App\Models\Reservation::with(['room'])
            ->where('user_id', $user->id)
            ->get();
    }

    /**
     * ========================================
     * CREAR NUEVA RESERVA
     * ========================================
     * 
     * Como cuando un huésped hace una nueva reservación.
     * El sistema AUTOMÁTICAMENTE vincula la reserva con el usuario logueado.
     * 
     * RUTA: POST /api/reservations
     * PROTEGIDA: Requiere token de autenticación
     * 
     * RECIBE:
     * - room_id: ID de la habitación a reservar
     * - check_in: Fecha de entrada (formato: YYYY-MM-DD)
     * - check_out: Fecha de salida (debe ser DESPUÉS de check_in)
     * - total_price: Precio total de la reserva
     * 
     * NO recibe user_id porque se obtiene automáticamente del token
     * 
     * DEVUELVE:
     * La reserva creada con status 'pending'
     * Código 201 (Created)
     */
    public function store(Request $request)
    {
        // PASO 1: Validar los datos recibidos
        $validated = $request->validate([
            // exists:rooms,id verifica que la habitación realmente exista
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',              // Debe ser una fecha válida
            // after:check_in asegura que check_out sea DESPUÉS de check_in
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|numeric',        // Debe ser un número
        ]);

        // PASO 2: Crear la reserva
        // IMPORTANTE: user_id se obtiene del usuario autenticado automáticamente
        $reservation = \App\Models\Reservation::create([
            'user_id' => $request->user()->id,     // Usuario que hace la reserva (del token)
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $validated['total_price'],
            'status' => 'pending',                 // Todas las reservas inician como 'pending'
        ]);

        // PASO 3: Devolver la reserva creada
        return response()->json($reservation, 201);
    }

    /**
     * ========================================
     * MOSTRAR UNA RESERVA ESPECÍFICA
     * ========================================
     * 
     * Ver los detalles completos de una reserva particular.
     * Incluye información del usuario, habitación y pago (si existe).
     * 
     * RUTA: GET /api/reservations/{id}
     * Ejemplo: GET /api/reservations/42
     * PROTEGIDA: Requiere autenticación
     * 
     * RECIBE:
     * - id: ID de la reserva
     * 
     * DEVUELVE:
     * Reserva con datos completos relacionados
     * Error 404 si no existe
     */
    public function show(string $id)
    {
        // with(['user', 'room', 'payment']) carga todas las relaciones
        // Así obtienes toda la información en UNA sola consulta
        return \App\Models\Reservation::with(['user', 'room', 'payment'])
            ->findOrFail($id);
    }

    /**
     * ========================================
     * ACTUALIZAR RESERVA
     * ========================================
     * 
     * Modificar una reserva existente.
     * En este caso, principalmente se usa para cambiar el STATUS.
     * 
     * Ejemplo de estados:
     * - 'pending' = Pendiente de confirmación
     * - 'confirmed' = Confirmada
     * - 'cancelled' = Cancelada
     * - 'completed' = Completada
     * 
     * RUTA: PUT /api/reservations/{id}
     * PROTEGIDA: Requiere autenticación
     * 
     * RECIBE:
     * - id: ID de la reserva
     * - status: Nuevo estado (en el body)
     * 
     * DEVUELVE:
     * La reserva actualizada
     */
    public function update(Request $request, string $id)
    {
        // PASO 1: Buscar la reserva
        $reservation = \App\Models\Reservation::findOrFail($id);
        
        // PASO 2: Actualizar solo el campo 'status'
        // only('status') asegura que solo se pueda modificar el estado
        // Esto previene que se modifiquen otros campos importantes como user_id o room_id
        $reservation->update($request->only('status'));

        // PASO 3: Devolver la reserva actualizada
        return response()->json($reservation);
    }

    /**
     * ========================================
     * CANCELAR/ELIMINAR RESERVA
     * ========================================
     * 
     * Eliminar permanentemente una reserva de la base de datos.
     * Esto podría ser usado cuando un usuario cancela su reserva
     * o un admin necesita limpiar registros antiguos.
     * 
     * RUTA: DELETE /api/reservations/{id}
     * PROTEGIDA: Requiere autenticación
     * 
     * RECIBE:
     * - id: ID de la reserva a eliminar
     * 
     * DEVUELVE:
     * Respuesta vacía con código 204 (No Content)
     * Indica eliminación exitosa
     */
    public function destroy(string $id)
    {
        // Busca y elimina la reserva de la base de datos
        \App\Models\Reservation::destroy($id);

        // Respuesta vacía con código 204
        return response()->json(null, 204);
    }
}

