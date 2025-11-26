# 🏨 Sistema de Reservas de Hotel

## 📋 Descripción del Proyecto

Este es un sistema completo de reservas de hotel construido con:
- **Backend**: Laravel 11 (PHP) con API REST
- **Frontend**: Angular 19 (TypeScript) con Material Design
- **Base de Datos**: PostgreSQL
- **Autenticación**: Laravel Sanctum (tokens Bearer)

## 🎯 Funcionalidades Principales

### Para Usuarios
- ✅ Registrarse y crear una cuenta
- ✅ Iniciar y cerrar sesión
- ✅ Ver catálogo de habitaciones disponibles
- ✅ Hacer reservas de habitaciones
- ✅ Ver sus propias reservas

### Para Administradores
- ✅ Todo lo que pueden hacer los usuarios normales
- ✅ Crear nuevas habitaciones
- ✅ Editar información de habitaciones
- ✅ Eliminar habitaciones
- ✅ Ver todas las reservas de todos los usuarios

## 📚 Documentación Completa

### 📖 Guía para Principiantes
Hemos creado una **guía super detallada** que explica TODO el proyecto como si no supieras nada de programación:

👉 **[LEE LA GUÍA COMPLETA AQUÍ](./GUIA_COMPLETA_PROYECTO.md)**

Esta guía incluye:
- Explicación de conceptos básicos (¿Qué es una API? ¿Qué es un token?)
- Cómo funciona el backend paso a paso
- Cómo funciona el frontend paso a paso
- Cómo se comunican entre sí
- Ejemplos detallados de flujos completos
- Glosario de términos técnicos

### 📝 Código Documentado

**TODOS los archivos del proyecto** están completamente documentados con comentarios explicativos:

#### Backend (Laravel)
- ✅ `backend/app/Http/Controllers/AuthController.php` - Registro, login, logout
- ✅ `backend/app/Http/Controllers/RoomController.php` - CRUD de habitaciones
- ✅ `backend/app/Http/Controllers/ReservationController.php` - CRUD de reservas
- ✅ `backend/routes/api.php` - Todas las rutas del API

#### Frontend (Angular)
- ✅ `frontend/src/app/interceptors/auth-interceptor.ts` - Agrega token automáticamente
- ✅ `frontend/src/app/services/auth.ts` - Servicio de autenticación
- ✅ `frontend/src/app/services/room.ts` - Servicio de habitaciones
- ✅ `frontend/src/app/services/reservation.ts` - Servicio de reservas

Todos los comentarios están escritos en español y explican:
- Qué hace cada función
- Qué recibe (parámetros)
- Qué devuelve
- Ejemplos de uso
- Conceptos técnicos explicados de forma simple

## 🚀 Cómo Ejecutar el Proyecto

### Requisitos Previos
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL
- Git

### Backend (Laravel)

```bash
# 1. Navegar a la carpeta del backend
cd backend

# 2. Instalar dependencias
composer install

# 3. Configurar base de datos
# Editar el archivo .env con tus credenciales de PostgreSQL

# 4. Ejecutar migraciones
php artisan migrate

# 5. Iniciar servidor
php artisan serve
# El backend estará en: http://localhost:8000
```

### Frontend (Angular)

```bash
# 1. Navegar a la carpeta del frontend
cd frontend

# 2. Instalar dependencias
npm install

# 3. Iniciar servidor de desarrollo
npm start
# El frontend estará en: http://localhost:4200
```

## 🗺️ Estructura del Proyecto

```
ChristianPerdomo/
│
├── backend/                          # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/         # Controladores (lógica de negocio)
│   │   │       ├── AuthController.php
│   │   │       ├── RoomController.php
│   │   │       └── ReservationController.php
│   │   │
│   │   └── Models/                  # Modelos (representan tablas)
│   │       ├── User.php
│   │       ├── Room.php
│   │       └── Reservation.php
│   │
│   ├── routes/
│   │   └── api.php                  # Definición de rutas API
│   │
│   └── database/
│       └── migrations/              # Estructura de la base de datos
│
├── frontend/                         # Angular App
│   └── src/
│       └── app/
│           ├── services/            # Servicios (comunicación HTTP)
│           │   ├── auth.ts
│           │   ├── room.ts
│           │   └── reservation.ts
│           │
│           ├── interceptors/        # Interceptores HTTP
│           │   └── auth-interceptor.ts
│           │
│           └── components/          # Componentes visuales
│               ├── login/
│               ├── register/
│               ├── rooms/
│               ├── reservations/
│               ├── admin-dashboard/
│               └── navbar/
│
└── GUIA_COMPLETA_PROYECTO.md        # Guía detallada (¡LÉELA!)
```

## 🔑 Rutas del API

### Públicas (no requieren autenticación)

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/register` | Registrar nuevo usuario |
| POST | `/api/login` | Iniciar sesión |
| GET | `/api/rooms` | Listar todas las habitaciones |
| GET | `/api/rooms/{id}` | Ver una habitación específica |

### Protegidas (requieren token)

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/user` | Obtener usuario actual |
| POST | `/api/rooms` | Crear habitación (admin) |
| PUT | `/api/rooms/{id}` | Actualizar habitación (admin) |
| DELETE | `/api/rooms/{id}` | Eliminar habitación (admin) |
| GET | `/api/reservations` | Listar reservas |
| POST | `/api/reservations` | Crear reserva |
| GET | `/api/reservations/{id}` | Ver una reserva |
| PUT | `/api/reservations/{id}` | Actualizar reserva |
| DELETE | `/api/reservations/{id}` | Cancelar reserva |

## 🎨 Tecnologías Utilizadas

### Backend
- **Laravel 11**: Framework PHP moderno
- **Laravel Sanctum**: Autenticación basada en tokens
- **Eloquent ORM**: Manejo de base de datos
- **PostgreSQL**: Base de datos relacional

### Frontend
- **Angular 19**: Framework JavaScript/TypeScript
- **Angular Material**: Componentes UI de Material Design
- **RxJS**: Programación reactiva con Observables
- **TypeScript**: JavaScript con tipos estáticos

## 📖 Conceptos Clave Explicados

### ¿Qué es una API REST?
Es un conjunto de "servicios" que el frontend puede consumir para obtener o modificar datos. Como un "menú de restaurante" donde puedes pedir diferentes cosas (listar habitaciones, crear reserva, etc.).

### ¿Cómo funciona la autenticación?
1. Usuario inicia sesión con email/contraseña
2. Backend verifica y genera un **token** (pase digital)
3. Frontend guarda el token
4. En cada petición, el frontend envía el token
5. Backend verifica el token para permitir (o denegar) acceso

### ¿Qué son los Observables?
Son como "promesas mejoradas" de JavaScript. Representan valores que llegarán en el futuro. Usamos `.subscribe()` para esperar y recibir el valor cuando esté listo.

### ¿Qué es un Interceptor?
Es un "guardia" que intercepta TODAS las peticiones HTTP antes de enviarlas, permitiéndote modificarlas. En nuestro caso, agregamos automáticamente el token de autenticación.

## 🆘 Solución de Problemas Comunes

### Backend no inicia
- Verifica que PostgreSQL esté corriendo
- Verifica las credenciales en `.env`
- Ejecuta `php artisan migrate` si no has creado las tablas

### Frontend no puede conectar con Backend
- Verifica que el backend esté corriendo en `http://localhost:8000`
- Revisa la consola del navegador para ver errores
- Verifica que las URLs en los servicios sean correctas

### Error "Unauthorized" (401)
- El token puede haber expirado
- Cierra sesión y vuelve a iniciar
- Verifica que el interceptor esté registrado correctamente

## 👥 Créditos

Proyecto creado para el curso de 5to semestre.

## 📞 Contacto

Si tienes dudas sobre cómo funciona el proyecto, **lee primero la [Guía Completa](./GUIA_COMPLETA_PROYECTO.md)** donde TODAS las funcionalidades están explicadas paso a paso.

---

**¡Importante!** Este proyecto está completamente documentado con comentarios en el código. Si quieres entender cómo funciona algo específico, abre el archivo correspondiente y lee los comentarios explicativos.
