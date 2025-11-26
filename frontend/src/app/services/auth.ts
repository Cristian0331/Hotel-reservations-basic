/*
 * ========================================
 * SERVICIO DE AUTENTICACIÓN (AuthService)
 * ========================================
 * 
 * Este servicio es el "MENSAJERO ESPECIALIZADO EN USUARIOS".
 * Se encarga de TODA la lógica relacionada con autenticación:
 * - Registrar nuevos usuarios
 * - Iniciar sesión
 * - Cerrar sesión
 * - Mantener el estado del usuario actual
 * - Verificar permisos (¿es admin? ¿está logueado?)
 * 
 * CONCEPTOS CLAVE:
 * 
 * 1. OBSERVABLE:
 *    Es como una "promesa mejorada". No da un valor inmediato,
 *    sino que promete que el valor llegará en el futuro.
 *    Usamos .subscribe() para esperar y recibir el valor.
 * 
 * 2. BehaviorSubject:
 *    Es una "caja mágica" que:
 *    - Guarda un valor actual
 *    - Permite que otros partes de la app "se suscriban" para saber cuando cambia
 *    - Automáticamente notifica a todos los suscriptores cuando hay un cambio
 *    Usado para mantener el estado del usuario en TODA la aplicación
 * 
 * 3. tap() operator:
 *    Permite "espiar" y hacer algo con los datos SIN modificarlos.
 *    Como un "efecto secundario" de la operación principal.
 */

import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';

/**
 * INTERFAZ: Estructura de un Usuario
 * Define qué campos tiene un usuario y sus tipos
 */
export interface User {
  id: number;         // ID único del usuario
  name: string;       // Nombre completo
  email: string;      // Correo electrónico
  role: string;       // Rol: 'admin' o 'user'
  phone?: string;     // Teléfono (opcional, el ? significa que puede no existir)
}

/**
 * INTERFAZ: Respuesta de Autenticación
 * Define qué devuelve el backend cuando te registras o inicias sesión
 */
export interface AuthResponse {
  access_token: string;   // El token (pase digital)
  token_type: string;     // Tipo de token (siempre 'Bearer')
  user: User;             // Datos del usuario
}

/**
 * @Injectable indica que este servicio puede ser "inyectado" en otros componentes
 * providedIn: 'root' significa que habrá UNA SOLA INSTANCIA en toda la app
 */
@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // URL base del backend API
  private apiUrl = 'http://localhost:8000/api';

  // BehaviorSubject: "Caja mágica" que guarda el usuario actual
  // Inicia con null (nadie logueado)
  // Privado porque solo este servicio debe modificarlo directamente
  private currentUserSubject = new BehaviorSubject<User | null>(null);

  // Observable público del usuario actual
  // Otros componentes se suscriben a esto para saber cuando el usuario cambia
  // El $ al final es convención para indicar que es un Observable
  public currentUser$ = this.currentUserSubject.asObservable();

  /**
   * CONSTRUCTOR
   * Se ejecuta UNA VEZ cuando se crea el servicio (al iniciar la app)
   * 
   * @param http - HttpClient para hacer peticiones HTTP
   *               Angular lo "inyecta" automáticamente
   */
  constructor(private http: HttpClient) {
    // Al iniciar la app, verificar si hay un usuario guardado en localStorage
    const user = localStorage.getItem('user');

    if (user) {
      // Si hay usuario guardado, restaurarlo en el BehaviorSubject
      // JSON.parse() convierte el texto JSON a un objeto JavaScript
      this.currentUserSubject.next(JSON.parse(user));
      // Ahora toda la app sabrá que hay un usuario logueado
    }
  }

  /**
   * ========================================
   * REGISTRAR NUEVO USUARIO
   * ========================================
   * 
   * Envía los datos de registro al backend.
   * Si es exitoso, guarda automáticamente el token y usuario.
   * 
   * @param data - Objeto con: name, email, password, phone
   * @returns Observable que emitirá la respuesta cuando llegue del servidor
   * 
   * USO en un componente:
   * authService.register(datos).subscribe({
   *   next: (response) => console.log('Éxito!', response),
   *   error: (error) => console.log('Error!', error)
   * });
   */
  register(data: any): Observable<AuthResponse> {
    // POST a http://localhost:8000/api/register
    return this.http.post<AuthResponse>(`${this.apiUrl}/register`, data).pipe(
      // tap() ejecuta handleAuth() cuando llegue la respuesta
      // SIN modificar la respuesta (solo como efecto secundario)
      tap(response => this.handleAuth(response))
    );
  }

  /**
   * ========================================
   * INICIAR SESIÓN
   * ========================================
   * 
   * Envía email y contraseña al backend.
   * Si son correctos, guarda el token y datos del usuario.
   * 
   * @param email - Correo electrónico del usuario
   * @param password - Contraseña
   * @returns Observable con la respuesta del servidor
   * 
   * USO:
   * authService.login('maria@gmail.com', 'password123').subscribe({
   *   next: () => console.log('Login exitoso'),
   *   error: () => console.log('Credenciales incorrectas')
   * });
   */
  login(email: string, password: string): Observable<AuthResponse> {
    // POST a http://localhost:8000/api/login
    // { email, password } es la sintaxis corta de { email: email, password: password }
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, { email, password }).pipe(
      // Cuando llegue la respuesta, ejecutar handleAuth() automáticamente
      tap(response => this.handleAuth(response))
    );
  }

  /**
   * ========================================
   * CERRAR SESIÓN
   * ========================================
   * 
   * Notifica al backend que el usuario cierra sesión,
   * elimina el token y datos del localStorage,
   * y resetea el estado a "nadie logueado".
   * 
   * @returns Observable vacío
   * 
   * USO:
   * authService.logout().subscribe({
   *   next: () => console.log('Sesión cerrada')
   * });
   */
  logout(): Observable<any> {
    // POST a http://localhost:8000/api/logout
    return this.http.post(`${this.apiUrl}/logout`, {}).pipe(
      tap(() => {
        // Eliminar token y usuario del localStorage
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        // Actualizar el BehaviorSubject a null (nadie logueado)
        this.currentUserSubject.next(null);
        // Ahora toda la app sabrá que el usuario cerró sesión
      })
    );
  }

  /**
   * ========================================
   * MANEJAR AUTENTICACIÓN (Función privada)
   * ========================================
   * 
   * Esta función se ejecuta automáticamente después de register() o login().
   * Guarda el token y usuario en localStorage y actualiza el estado.
   * 
   * Es PRIVADA porque solo este servicio debe usarla.
   * 
   * @param response - La respuesta del backend con token y usuario
   */
  private handleAuth(response: AuthResponse): void {
    // PASO 1: Guardar el token en localStorage
    // localStorage persiste entre recargas del navegador
    localStorage.setItem('token', response.access_token);

    // PASO 2: Guardar el usuario en localStorage
    // JSON.stringify() convierte el objeto a texto JSON para guardarlo
    localStorage.setItem('user', JSON.stringify(response.user));

    // PASO 3: Actualizar el BehaviorSubject con el usuario
    // Esto notifica a TODA la app que hay un usuario logueado
    this.currentUserSubject.next(response.user);
  }

  /**
   * OBTENER TOKEN
   * @returns El token guardado o null si no existe
   */
  getToken(): string | null {
    return localStorage.getItem('token');
  }

  /**
   * OBTENER USUARIO ACTUAL
   * @returns El usuario logueado o null si no hay nadie
   */
  getCurrentUser(): User | null {
    // .value obtiene el valor ACTUAL del BehaviorSubject
    return this.currentUserSubject.value;
  }

  /**
   * VERIFICAR SI ESTÁ LOGUEADO
   * @returns true si hay token, false si no
   * 
   * El !! convierte cualquier valor a booleano:
   * !!"1|abc" → true (hay valor)
   * !!null → false (no hay valor)
   */
  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  /**
   * VERIFICAR SI ES ADMINISTRADOR
   * @returns true si el usuario es admin, false si no
   * 
   * El operador ?. (optional chaining) significa:
   * Si user existe, accede a role, si no existe, devuelve undefined
   * Evita errores si user es null
   */
  isAdmin(): boolean {
    const user = this.getCurrentUser();
    return user?.role === 'admin';
  }
}

/*
 * ========================================
 * RESUMEN DE USO
 * ========================================
 * 
 * En un componente:
 * 
 * constructor(private authService: AuthService) {}
 * 
 * // Registrarse
 * this.authService.register(datos).subscribe(...);
 * 
 * // Iniciar sesión
 * this.authService.login(email, password).subscribe(...);
 * 
 * // Cerrar sesión
 * this.authService.logout().subscribe(...);
 * 
 * // Verificar si está logueado
 * if (this.authService.isLoggedIn()) { ... }
 * 
 * // Verificar si es admin
 * if (this.authService.isAdmin()) { ... }
 * 
 * // Observar cambios en el usuario
 * this.authService.currentUser$.subscribe(user => {
 *   console.log('Usuario actual:', user);
 * });
 */
