<?php

/*
 * ========================================
 * CONTROLADOR DE AUTENTICACIÓN
 * ========================================
 * 
 * Este controlador es como el "recepcionista del hotel".
 * Se encarga de:
 * - Registrar nuevos usuarios (darles una cuenta)
 * - Permitirles iniciar sesión (verificar su identidad)
 * - Cerrar sesión (destruir su pase de acceso)
 * 
 * IMPORTANTE: Usa Laravel Sanctum para crear "tokens" (pases digitales)
 * que identifican al usuario en cada petición
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * ========================================
     * REGISTRAR NUEVO USUARIO
     * ========================================
     * 
     * Como cuando llegas por primera vez al hotel y te registras.
     * Te piden tus datos, verifican que sean correctos,
     * te crean una cuenta y te dan una tarjeta de acceso.
     * 
     * RECIBE:
     * - name: Nombre completo (ejemplo: "María García")
     * - email: Correo electrónico (debe ser único)
     * - password: Contraseña (mínimo 8 caracteres)
     * - phone: Teléfono (opcional)
     * 
     * DEVUELVE:
     * - access_token: El "pase digital" para futuras peticiones
     * - token_type: Siempre es "Bearer" (tipo de autenticación)
     * - user: Los datos del usuario creado
     */
    public function register(Request $request)
    {
        // PASO 1: Validar los datos recibidos
        // Es como revisar que llenaste bien el formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',           // Nombre es obligatorio, máximo 255 caracteres
            'email' => 'required|string|email|max:255|unique:users',  // Email único y válido
            'password' => 'required|string|min:8',         // Contraseña mínimo 8 caracteres
            'phone' => 'nullable|string',                  // Teléfono es opcional
        ]);

        // PASO 2: Crear el usuario en la base de datos
        // Es como agregar tu información a la lista de huéspedes
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            // IMPORTANTE: Encriptamos la contraseña (nunca se guarda en texto plano)
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,        // Si no hay teléfono, guardamos null
            'role' => 'user',                              // Por defecto todos son usuarios normales
        ]);

        // PASO 3: Crear un token (pase digital) para el usuario
        // Es como darle una tarjeta de acceso al hotel
        $token = $user->createToken('auth_token')->plainTextToken;

        // PASO 4: Devolver respuesta exitosa
        // Le decimos al frontend: "Todo salió bien, aquí está tu información"
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);  // 201 = Created (recurso creado exitosamente)
    }

    /**
     * ========================================
     * INICIAR SESIÓN
     * ========================================
     * 
     * Como cuando ya tienes cuenta en el hotel y vuelves.
     * Presentas tu email y contraseña, verifican que sean correctos,
     * y te dan una nueva tarjeta de acceso.
     * 
     * RECIBE:
     * - email: Tu correo electrónico
     * - password: Tu contraseña
     * 
     * DEVUELVE:
     * - access_token: Un nuevo "pase digital"
     * - token_type: "Bearer"
     * - user: Tus datos actualizados
     */
    public function login(Request $request)
    {
        // PASO 1: Intentar autenticar con email y contraseña
        // Laravel revisa automáticamente si el email existe y si la contraseña coincide
        if (!\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'))) {
            // Si las credenciales son incorrectas, devolvemos error 401
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);  // 401 = Unauthorized (no autorizado)
        }

        // PASO 2: Si las credenciales son correctas, buscamos al usuario
        $user = \App\Models\User::where('email', $request['email'])->firstOrFail();

        // PASO 3: Creamos un nuevo token para esta sesión
        // Cada vez que inicias sesión, obtienes un nuevo "pase"
        $token = $user->createToken('auth_token')->plainTextToken;

        // PASO 4: Devolvemos el token y datos del usuario
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);  // 200 = OK (por defecto)
    }

    /**
     * ========================================
     * CERRAR SESIÓN
     * ========================================
     * 
     * Como cuando te vas del hotel y devuelves tu tarjeta de acceso.
     * Destruimos tu token actual para que ya no pueda usarse.
     * 
     * IMPORTANTE: Esta ruta está protegida, solo usuarios logueados pueden llamarla
     * 
     * RECIBE:
     * - El token en el header Authorization (lo maneja el middleware)
     * 
     * DEVUELVE:
     * - Mensaje de confirmación
     */
    public function logout(Request $request)
    {
        // Obtiene el token actual del usuario (del header Authorization)
        // y lo elimina de la base de datos
        $request->user()->currentAccessToken()->delete();

        // Devolvemos confirmación
        return response()->json(['message' => 'Logged out successfully']);
    }
}
