/*
 * ========================================
 * AUTH INTERCEPTOR (Interceptor de Autenticación)
 * ========================================
 * 
 * Este archivo es como un "ASISTENTE AUTOMÁTICO" que trabaja detrás de escena.
 * 
 * ANALOGÍA:
 * Imagina que eres un mensajero que lleva cartas (peticiones HTTP).
 * Antes de enviar cada carta, un asistente verifica si tienes un "sello VIP"
 * (el token) y lo pega automáticamente en todas tus cartas.
 * 
 * ¿QUÉ HACE?
 * Intercepta TODAS las peticiones HTTP que hace la aplicación
 * ANTES de enviarlas al backend, y les agrega automáticamente
 * el token de autenticación si existe.
 * 
 * ¿POR QUÉ ES IMPORTANTE?
 * Sin este interceptor, tendríamos que agregar manualmente el header
 * de autorización en CADA petición. Esto nos ahorra mucho código repetitivo.
 * 
 * FLUJO:
 * 1. Frontend hace petición: GET /api/reservations
 * 2. Interceptor captura la petición
 * 3. Busca el token en localStorage
 * 4. Si hay token: lo agrega al header Authorization
 * 5. Envía la petición modificada al backend
 */

import { HttpInterceptorFn } from '@angular/common/http';

/**
 * Función interceptora de autenticación
 * 
 * PARÁMETROS:
 * @param req - La petición HTTP original que se va a enviar
 * @param next - Función que envía la petición al siguiente paso (al backend)
 * 
 * RETORNA:
 * La petición modificada con el token agregado (si existe)
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  // PASO 1: Buscar el token en localStorage
  // localStorage es como una "memoria" del navegador que persiste entre recargas
  const token = localStorage.getItem('token');

  // PASO 2: Verificar si existe un token
  if (token) {
    // SÍ hay token guardado

    // PASO 3: Clonar la petición original
    // No podemos modificar req directamente, debemos crear una copia
    const cloned = req.clone({
      // PASO 4: Agregar el header de autorización
      // 'Bearer' es el tipo de autenticación que usamos
      headers: req.headers.set('Authorization', `Bearer ${token}`)
    });

    // PASO 5: Enviar la petición MODIFICADA (con el token)
    return next(cloned);
  }

  // Si NO hay token, enviar la petición original sin modificar
  // Esto sucede cuando el usuario no ha iniciado sesión
  return next(req);
};

/*
 * EJEMPLO DE CÓMO FUNCIONA:
 * 
 * ANTES del interceptor:
 * -----------------------
 * GET /api/reservations
 * Headers: {
 *   Content-Type: 'application/json'
 * }
 * 
 * DESPUÉS del interceptor (si hay token):
 * ---------------------------------------
 * GET /api/reservations
 * Headers: {
 *   Content-Type: 'application/json',
 *   Authorization: 'Bearer 1|abcd1234xyz...'  ← AGREGADO AUTOMÁTICAMENTE
 * }
 * 
 * El backend recibe el header Authorization, extrae el token,
 * lo valida y permite (o deniega) el acceso.
 */

