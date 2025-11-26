<?php

/*
 * ========================================
 * CONTROLADOR DE HABITACIONES (ROOMS)
 * ========================================
 * 
 * Este controlador es como el "administrador de habitaciones del hotel".
 * Se encarga de:
 * - Listar todas las habitaciones disponibles
 * - Mostrar información detallada de UNA habitación específica
 * - Crear nuevas habitaciones (solo admin)
 * - Actualizar información de habitaciones existentes (solo admin)
 * - Eliminar habitaciones (solo admin)
 * 
 * IMPORTANTE: Las rutas de crear, actualizar y eliminar están protegidas
 * y requieren autenticación (middleware 'auth:sanctum')
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * ========================================
     * LISTAR TODAS LAS HABITACIONES
     * ========================================
     * 
     * Como ver el catálogo completo de habitaciones del hotel.
     * Esta función es PÚBLICA, cualquiera puede ver las habitaciones
     * sin necesidad de estar logueado.
     * 
     * RUTA: GET /api/rooms
     * 
     * NO RECIBE parámetros
     * 
     * DEVUELVE:
     * Array con todas las habitaciones, ejemplo:
     * [
     *   { id: 1, name: "Suite Presidencial", price: 200, ... },
     *   { id: 2, name: "Habitación Doble", price: 80, ... },
     *   ...
     * ]
     */
    public function index()
    {
        // Room::all() obtiene TODAS las habitaciones de la base de datos
        // Laravel automáticamente las convierte a JSON
        return \App\Models\Room::all();
    }

    /**
     * ========================================
     * CREAR NUEVA HABITACIÓN
     * ========================================
     * 
     * Como agregar una nueva habitación al catálogo del hotel.
     * Solo los administradores pueden hacer esto.
     * 
     * RUTA: POST /api/rooms
     * PROTEGIDA: Requiere token de autenticación
     * 
     * RECIBE (en el body de la petición):
     * - name: Nombre de la habitación (ej: "Suite Presidencial VIP")
     * - description: Descripción detallada (opcional)
     * - price: Precio por noche en dólares (número)
     * - capacity: Capacidad de personas (número entero)
     * - type: Tipo de habitación (ej: "Suite", "Estándar", "Deluxe")
     * - image_url: URL de la imagen (opcional)
     * - is_available: ¿Está disponible? (true/false, opcional)
     * 
     * DEVUELVE:
     * La habitación recién creada con su ID
     * Código 201 (Created)
     */
    public function store(Request $request)
    {
        // PASO 1: Validar que todos los datos sean correctos
        // Es como revisar que el formulario esté bien llenado
        $validated = $request->validate([
            'name' => 'required|string|max:255',        // Nombre obligatorio
            'description' => 'nullable|string',         // Descripción opcional
            'price' => 'required|numeric',              // Precio obligatorio, debe ser número
            'capacity' => 'required|integer',           // Capacidad obligatoria, debe ser entero
            'type' => 'required|string',                // Tipo obligatorio
            'image_url' => 'nullable|string',           // URL imagen opcional
            'is_available' => 'boolean',                // Booleano opcional (true/false)
        ]);

        // PASO 2: Crear la habitación en la base de datos
        // Room::create() inserta los datos y devuelve el objeto creado
        $room = \App\Models\Room::create($validated);

        // PASO 3: Devolver la habitación creada
        // El 201 indica que se creó un nuevo recurso exitosamente
        return response()->json($room, 201);
    }

    /**
     * ========================================
     * MOSTRAR UNA HABITACIÓN ESPECÍFICA
     * ========================================
     * 
     * Como pedir información detallada de UNA habitación en particular.
     * Por ejemplo, cuando haces click en una habitación para ver más detalles.
     * 
     * RUTA: GET /api/rooms/{id}
     * Ejemplo: GET /api/rooms/5  (muestra la habitación con ID 5)
     * 
     * RECIBE:
     * - id: El identificador único de la habitación
     * 
     * DEVUELVE:
     * Los datos completos de esa habitación
     * Si no existe, devuelve error 404 (Not Found)
     */
    public function show(string $id)
    {
        // findOrFail() hace lo siguiente:
        // 1. Busca una habitación con ese ID
        // 2. Si la encuentra, la devuelve
        // 3. Si NO la encuentra, automáticamente devuelve error 404
        return \App\Models\Room::findOrFail($id);
    }

    /**
     * ========================================
     * ACTUALIZAR HABITACIÓN EXISTENTE
     * ========================================
     * 
     * Como modificar la información de una habitación que ya existe.
     * Por ejemplo, cambiar el precio o actualizar la descripción.
     * 
     * RUTA: PUT /api/rooms/{id}
     * Ejemplo: PUT /api/rooms/5  (actualiza la habitación #5)
     * PROTEGIDA: Requiere token de autenticación (solo admin)
     * 
     * RECIBE:
     * - id: ID de la habitación a actualizar
     * - Los campos que quieres cambiar (en el body)
     * 
     * DEVUELVE:
     * La habitación actualizada con los nuevos valores
     */
    public function update(Request $request, string $id)
    {
        // PASO 1: Buscar la habitación
        // Si no existe, devuelve error 404 automáticamente
        $room = \App\Models\Room::findOrFail($id);
        
        // PASO 2: Actualizar con los nuevos datos
        // $request->all() obtiene todos los campos enviados
        // update() modifica solo los campos que cambiaron
        $room->update($request->all());

        // PASO 3: Devolver la habitación actualizada
        return response()->json($room);
    }

    /**
     * ========================================
     * ELIMINAR HABITACIÓN
     * ========================================
     * 
     * Como quitar una habitación del catálogo del hotel.
     * IMPORTANTE: Esto ELIMINA permanentemente el registro de la base de datos.
     * 
     * RUTA: DELETE /api/rooms/{id}
     * Ejemplo: DELETE /api/rooms/5  (elimina la habitación #5)
     * PROTEGIDA: Requiere token de autenticación (solo admin)
     * 
     * RECIBE:
     * - id: ID de la habitación a eliminar
     * 
     * DEVUELVE:
     * Respuesta vacía con código 204 (No Content)
     * Indica que se eliminó exitosamente
     */
    public function destroy(string $id)
    {
        // Room::destroy($id) hace lo siguiente:
        // 1. Busca la habitación con ese ID
        // 2. La elimina de la base de datos
        \App\Models\Room::destroy($id);

        // Devolvemos respuesta vacía (null) con código 204
        // 204 = No Content (operación exitosa sin contenido que devolver)
        return response()->json(null, 204);
    }
}

