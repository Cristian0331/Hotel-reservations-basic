# 🏨 Grand Hotel - Sistema de Reservaciones

Sistema completo de gestión de reservaciones de hotel con Frontend Angular y Backend Laravel.

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Node.js** (v18 o superior) - [Descargar](https://nodejs.org/)
- **PHP** (v8.2 o superior) - [Descargar](https://www.php.net/downloads)
- **Composer** - [Descargar](https://getcomposer.org/download/)
- **PostgreSQL** - [Descargar](https://www.postgresql.org/download/)
- **Git** - [Descargar](https://git-scm.com/downloads)

## 🚀 Instalación Paso a Paso

### 1️⃣ Clonar el Repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd Hotel-reservations-basic
```

---

### 2️⃣ Configurar Backend (Laravel)

#### a) Navegar a la carpeta backend
```bash
cd backend
```

#### b) Instalar dependencias de PHP
```bash
composer install
```

#### c) Copiar archivo de configuración
```bash
# En Windows (PowerShell)
copy .env.example .env

# En Mac/Linux
cp .env.example .env
```

#### d) Generar clave de aplicación
```bash
php artisan key:generate
```

#### e) Configurar base de datos

Abre el archivo `.env` y configura tu base de datos PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hotel_db
DB_USERNAME=postgres
DB_PASSWORD=TU_CONTRASEÑA_AQUI
```

#### f) Crear la base de datos

Abre **pgAdmin** o **psql** y crea la base de datos:

```sql
CREATE DATABASE hotel_db;
```

#### g) Ejecutar migraciones y seeders
```bash
php artisan migrate:fresh --seed
```

#### h) Iniciar servidor Laravel
```bash
php artisan serve
```

El backend estará corriendo en: **http://127.0.0.1:8000**

---

### 3️⃣ Configurar Frontend (Angular)

#### a) Abrir nueva terminal y navegar al frontend
```bash
cd frontend
```

#### b) Instalar dependencias de Node.js
```bash
npm install
```

#### c) Iniciar servidor de desarrollo
```bash
npm start
```

El frontend estará corriendo en: **http://localhost:4200**

---

## 🎯 Acceso al Sistema

### Usuarios de Prueba (creados por el seeder)

**Administrador:**
- Email: `admin@hotel.com`
- Contraseña: `password123`

**Usuario Normal:**
- Email: `user@hotel.com`
- Contraseña: `password123`

---

## 📂 Estructura del Proyecto

```
Hotel-reservations-basic/
├── backend/                 # Laravel API
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── .env
├── frontend/                # Angular App
│   ├── src/
│   │   ├── app/
│   │   └── styles.css
│   └── angular.json
└── README.md
```

---

## 🛠️ Comandos Útiles

### Backend (Laravel)

```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Resetear base de datos
php artisan migrate:fresh --seed

# Iniciar servidor
php artisan serve
```

### Frontend (Angular)

```bash
# Instalar dependencias
npm install

# Iniciar servidor desarrollo
npm start

# Compilar para producción
npm run build

# Limpiar caché de node_modules
rm -rf node_modules
npm install
```

---

## 🐛 Solución de Problemas Comunes

### Error: "vendor/autoload.php not found"
**Solución:** Instala las dependencias de Composer
```bash
cd backend
composer install
```

### Error: "SQLSTATE[08006] connection refused"
**Solución:** 
1. Verifica que PostgreSQL esté corriendo
2. Revisa las credenciales en `.env`
3. Asegúrate de que la base de datos `hotel_db` exista

### Error: "Port 4200 is already in use"
**Solución:** Mata el proceso anterior
```bash
# Windows
taskkill /F /IM node.exe

# Mac/Linux
killall node
```

### Error de CORS
**Solución:** El backend ya tiene CORS configurado. Asegúrate de que:
- Backend corra en `http://127.0.0.1:8000`
- Frontend corra en `http://localhost:4200`

---

## 🌟 Características

- ✅ Autenticación de usuarios (Login/Register)
- ✅ Gestión de habitaciones
- ✅ Sistema de reservaciones
- ✅ Panel de administración
- ✅ Dashboard de usuario
- ✅ Diseño responsive
- ✅ Tema claro y elegante

---

## 🔐 Seguridad

- Autenticación con Laravel Sanctum
- Tokens de sesión
- Validación de datos
- Protección CSRF

---

## 📱 Tecnologías Utilizadas

### Backend
- Laravel 12
- PHP 8.2+
- PostgreSQL
- Laravel Sanctum

### Frontend
- Angular 21
- TypeScript
- Angular Material
- RxJS

---

## 👥 Autores

- Cristian Perdomo
- Jorge Luis Trujillo

---

## 📄 Licencia

Este proyecto es de código abierto para fines educativos.

---

## 📞 Soporte

Si encuentras algún problema, abre un issue en el repositorio o contacta al equipo de desarrollo.

---

**¡Disfruta usando Grand Hotel System! 🏨✨**
