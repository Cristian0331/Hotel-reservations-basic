# 📚 Índice de Documentación del Proyecto

## 🎯 Bienvenida

¡Bienvenido a la documentación completa del Sistema de Reservas de Hotel!

Esta documentación está diseñada para que **cualquier persona**, sin importar su nivel de conocimiento en programación, pueda entender cómo funciona el proyecto.

---

## 📖 Documentos Principales

### 1. **[README.md](./README.md)** - Inicio Rápido
- Descripción general del proyecto
- Cómo ejecutar el proyecto
- Estructura de carpetas
- Tecnologías utilizadas
- Solución de problemas comunes

**👉 Empieza aquí si quieres ejecutar el proyecto rápidamente**

---

### 2. **[GUIA_COMPLETA_PROYECTO.md](./GUIA_COMPLETA_PROYECTO.md)** - Guía Detallada para Principiantes
- Explicación de conceptos básicos desde cero
- ¿Qué es Frontend y Backend?
- ¿Qué es una API?
- ¿Cómo funciona la autenticación con tokens?
- Explicación paso a paso de TODA la lógica del backend
- Explicación paso a paso de TODA la lógica del frontend
- Flujos completos de ejemplo (Login, Crear habitación, Hacer reserva)
- Glosario de términos técnicos

**👉 Empieza aquí si quieres ENTENDER cómo funciona todo el proyecto**

---

## 💻 Archivos de Código Documentados

### Backend (Laravel - PHP)

#### Controladores
Contienen la lógica de negocio de cada funcionalidad:

1. **[AuthController.php](./backend/app/Http/Controllers/AuthController.php)**
   - Registro de usuarios
   - Inicio de sesión
   - Cierre de sesión
   - Generación y manejo de tokens

2. **[RoomController.php](./backend/app/Http/Controllers/RoomController.php)**
   - Listar todas las habitaciones
   - Ver una habitación específica
   - Crear nueva habitación (admin)
   - Actualizar habitación (admin)
   - Eliminar habitación (admin)

3. **[ReservationController.php](./backend/app/Http/Controllers/ReservationController.php)**
   - Listar reservas (con filtro por rol)
   - Crear nueva reserva
   - Ver una reserva específica
   - Actualizar estado de reserva
   - Cancelar reserva

#### Rutas
4. **[api.php](./backend/routes/api.php)**
   - Definición de TODAS las rutas del API
   - Rutas públicas vs protegidas
   - Explicación de middleware de autenticación

---

### Frontend (Angular - TypeScript)

#### Interceptores
5. **[auth-interceptor.ts](./frontend/src/app/interceptors/auth-interceptor.ts)**
   - Agrega automáticamente el token a TODAS las peticiones
   - Explicación de cómo funciona un interceptor

#### Servicios
Manejan la comunicación con el backend:

6. **[auth.ts](./frontend/src/app/services/auth.ts)** - Servicio de Autenticación
   - Registro de usuarios
   - Inicio de sesión
   - Cierre de sesión
   - Manejo de estado del usuario con BehaviorSubject
   - Funciones de verificación (isLoggedIn, isAdmin)

7. **[room.ts](./frontend/src/app/services/room.ts)** - Servicio de Habitaciones
   - Obtener todas las habitaciones
   - Obtener una habitación específica
   - Crear habitación
   - Actualizar habitación
   - Eliminar habitación

8. **[reservation.ts](./frontend/src/app/services/reservation.ts)** - Servicio de Reservas
   - Obtener todas las reservas
   - Obtener una reserva específica
   - Crear reserva
   - Actualizar reserva
   - Eliminar reserva

---

## 🗺️ Rutas de Navegación Según tu Objetivo

### 🎓 "Quiero APRENDER cómo funciona todo"
1. Lee [GUIA_COMPLETA_PROYECTO.md](./GUIA_COMPLETA_PROYECTO.md) completa
2. Revisa los archivos de código en orden:
   - Backend: api.php → AuthController → RoomController → ReservationController
   - Frontend: auth-interceptor → auth service → room service → reservation service

### 🚀 "Solo quiero ejecutar el proyecto"
1. Sigue las instrucciones en [README.md](./README.md)
2. Si algo no funciona, ve a "Solución de Problemas"

### 🔍 "Quiero entender una funcionalidad específica"

#### Autenticación (Login/Register)
- Lee sección "Backend → AuthController" en [GUIA_COMPLETA_PROYECTO.md](./GUIA_COMPLETA_PROYECTO.md)
- Revisa código: [AuthController.php](./backend/app/Http/Controllers/AuthController.php)
- Revisa código: [auth.ts](./frontend/src/app/services/auth.ts)
- Lee ejemplo completo: "Ejemplo 1: Usuario Inicia Sesión" en la guía

#### Gestión de Habitaciones
- Lee sección "RoomController" en la guía
- Revisa código: [RoomController.php](./backend/app/Http/Controllers/RoomController.php)
- Revisa código: [room.ts](./frontend/src/app/services/room.ts)
- Lee ejemplo: "Ejemplo 2: Administrador Crea una Habitación" en la guía

#### Sistema de Reservas
- Lee sección "ReservationController" en la guía
- Revisa código: [ReservationController.php](./backend/app/Http/Controllers/ReservationController.php)
- Revisa código: [reservation.ts](./frontend/src/app/services/reservation.ts)
- Lee ejemplo: "Ejemplo 3: Usuario Hace una Reserva" en la guía

#### Cómo se agregan tokens automáticamente
- Lee sección "El Interceptor" en la guía
- Revisa código: [auth-interceptor.ts](./frontend/src/app/interceptors/auth-interceptor.ts)

---

## 📊 Diagrama Visual del Flujo

```
┌─────────────────────────────────────────────────────────┐
│                   USUARIO EN NAVEGADOR                  │
│                    (Frontend Angular)                   │
└─────────────────────┬───────────────────────────────────┘
                      │
                      │ HTTP Request (con token automático)
                      ↓
┌─────────────────────────────────────────────────────────┐
│              SERVIDOR (Backend Laravel)                 │
│                                                         │
│  api.php → Middleware → Controller → Model → Database  │
└─────────────────────┬───────────────────────────────────┘
                      │
                      │ HTTP Response (JSON)
                      ↓
┌─────────────────────────────────────────────────────────┐
│               Service → Component → Vista               │
│                    (Frontend Angular)                   │
└─────────────────────────────────────────────────────────┘
```

---

## 🎯 Conceptos Clave por Nivel

### Nivel Principiante
- **API**: Sistema de comunicación entre frontend y backend
- **Token**: "Pase digital" que demuestra que estás autenticado
- **Observable**: Promesa de un valor que llegará en el futuro
- **JSON**: Formato de texto para intercambiar datos

### Nivel Intermedio
- **Middleware**: Guardias que verifican cosas antes de ejecutar código
- **Interceptor**: Modifica automáticamente todas las peticiones HTTP
- **BehaviorSubject**: Estado compartido reactivo
- **CRUD**: Create, Read, Update, Delete (operaciones básicas)

### Nivel Avanzado
- **Sanctum**: Sistema de autenticación basado en tokens para SPAs
- **Eloquent ORM**: Abstracción de base de datos en Laravel
- **RxJS**: Librería de programación reactiva
- **Dependency Injection**: Patrón de diseño en Angular

---

## ❓ Preguntas Frecuentes

### "¿Por dónde empiezo?"
Si no sabes NADA de programación: Empieza por [GUIA_COMPLETA_PROYECTO.md](./GUIA_COMPLETA_PROYECTO.md)

### "¿Cómo funciona la autenticación?"
Lee la sección "Sistema de Autenticación" en la guía completa + el ejemplo práctico.

### "¿Por qué necesitamos un interceptor?"
Para no tener que agregar manualmente el token en cada petición. Revisa [auth-interceptor.ts](./frontend/src/app/interceptors/auth-interceptor.ts)

### "¿Qué hace cada archivo?"
Cada archivo de código tiene comentarios explicando su propósito. Ábrelos y léelos.

### "¿Qué es un Observable?"
Es una "promesa mejorada". Explicado detalladamente en [GUIA_COMPLETA_PROYECTO.md](./GUIA_COMPLETA_PROYECTO.md) sección "Conceptos básicos".

---

## 🎓 Recomendación de Lectura

**Para entender el proyecto completo en orden lógico:**

1. **Conceptos básicos** → Guía Completa: Sección "Conceptos básicos"
2. **Backend (Cocina)** → Guía Completa: Sección "Backend Explicado"
3. **Frontend (Mesero)** → Guía Completa: Sección "Frontend Explicado"
4. **Comunicación** → Guía Completa: Sección "Comunicación APIs"
5. **Ejemplos prácticos** → Guía Completa: Sección "Flujo Completo"

**Luego, revisa el código comentado en este orden:**
1. `backend/routes/api.php` (el mapa de rutas)
2. `backend/app/Http/Controllers/AuthController.php`
3. `frontend/src/app/interceptors/auth-interceptor.ts`
4. `frontend/src/app/services/auth.ts`
5. Los demás controladores y servicios según te interesen

---

## 💡 Consejos

✅ **Lee los comentarios en el código**: Cada archivo tiene explicaciones detalladas

✅ **Usa el glosario**: Al final de la guía completa hay un glosario con todos los términos

✅ **Sigue los ejemplos**: Los ejemplos paso a paso te muestran EXACTAMENTE cómo fluye todo

✅ **No te abrumes**: Lee por secciones, no todo de una vez

✅ **Experimenta**: Ejecuta el proyecto y prueba las funcionalidades mientras lees

---

¡Feliz aprendizaje! 🚀

*Última actualización: Noviembre 2025*
