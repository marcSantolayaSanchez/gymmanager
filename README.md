# 🏋️ GymManager

<div align="center">

**Sistema de gestión integral para gimnasios**  
Construido con Laravel 12 · MySQL · Blade · PHP 8.3

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-C8FF00?style=for-the-badge)](LICENSE)

</div>

---

## ¿Qué es GymManager?

GymManager es una aplicación web full-stack diseñada para la **gestión completa de un gimnasio**. Permite a los administradores controlar clientes, entrenadores, rutinas y membresías desde un panel centralizado con un sistema de roles y permisos granular.

Desarrollado como **proyecto de portfolio**, demuestra el uso profesional de Laravel con patrones reales de arquitectura: Eloquent ORM con relaciones complejas, autorización mediante Policies, autenticación con Sanctum y una API REST documentada.

---

## ✨ Funcionalidades

### 👥 Gestión de Clientes
- Registro completo con datos físicos (peso, altura, fecha de nacimiento)
- Asignación de membresías con cálculo automático de fechas de vencimiento
- Sistema de estados: **Activa · Por vencer · Expirada**
- Historial de rutinas por cliente
- Búsqueda y filtrado en tiempo real

### 🏋️ Gestión de Entrenadores
- Perfiles con especialidad y biografía
- Vista de clientes y rutinas asignadas
- Solo el administrador puede crear o eliminar entrenadores

### 📋 Rutinas de Entrenamiento
- Creación y asignación de rutinas a clientes específicos
- Estados: **Pendiente · Completada · Cancelada**
- Filtrado por estado y entrenador
- Los entrenadores solo ven y gestionan sus propias rutinas

### 💳 Membresías
- Tres planes configurables: Básica, Premium, VIP
- Precios, duración y descripción editables
- Gráfico de distribución de clientes por plan
- Renovación de membresía desde el perfil del cliente

### 📊 Dashboard con métricas en tiempo real
- Total de clientes activos
- Ingresos mensuales recurrentes
- Alertas de membresías próximas a vencer
- Últimas rutinas y clientes registrados

### 🔐 Sistema de Roles
| Rol | Permisos |
|---|---|
| **Admin** | Acceso total. Crea y elimina entrenadores y clientes. |
| **Entrenador** | Ve y gestiona sus propios clientes y rutinas. |
| **Cliente** | Ve su propio perfil, membresía y rutinas. |

### 📧 Notificaciones automáticas
- Email automático cuando una membresía está próxima a vencer
- Comando Artisan programable en el scheduler diario

---

## 🛠 Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12, PHP 8.3 |
| Base de datos | MySQL 8.0 |
| Autenticación | Laravel Sanctum |
| Frontend | Blade Templates, CSS puro |
| Tipografía | Syne + DM Sans (Google Fonts) |
| API | REST con tokens Bearer |
| Tests | PHPUnit / Feature Tests |

---

## 🔗 Relaciones Eloquent implementadas

```
User ──────── hasOne ──────► Client
User ──────── hasOne ──────► Trainer
Client ─────── belongsTo ──► Membership
Membership ─── hasMany ────► Client
Trainer ─────── hasMany ───► Workout
Client ──────── hasMany ───► Workout
Trainer ── hasManyThrough ─► Client (via Workouts)
```

Todas las consultas usan **eager loading** para evitar el problema N+1.

---

## 🚀 Instalación local

### Requisitos
- PHP 8.2+
- Composer
- MySQL 8.0+
- Laragon / XAMPP / Laravel Herd

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/TU-USUARIO/gymmanager.git
cd gymmanager

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gymmanager
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones y datos de prueba
php artisan migrate:fresh --seed

# 6. Iniciar servidor
php artisan serve
```

Abre `http://127.0.0.1:8000` en tu navegador.

---

## 🔑 Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@gymmanager.com | password |
| Entrenador | carlos@gymmanager.com | password |
| Cliente | sara@example.com | password |

---

## 📡 API REST

La aplicación incluye una API REST completa accesible bajo `/api/`.

### Autenticación

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@gymmanager.com",
  "password": "password"
}
```

```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Administrador",
    "role": "admin"
  }
}
```

Incluye el token en todas las peticiones:
```
Authorization: Bearer {token}
```

### Endpoints principales

| Método | Endpoint | Descripción |
|---|---|---|
| `POST` | `/api/auth/login` | Login |
| `GET` | `/api/auth/me` | Usuario autenticado |
| `GET` | `/api/dashboard` | Métricas generales |
| `GET/POST` | `/api/clients` | Listar / Crear clientes |
| `GET/PUT/DELETE` | `/api/clients/{id}` | Ver / Editar / Eliminar |
| `GET/POST` | `/api/trainers` | Listar / Crear entrenadores |
| `GET/POST` | `/api/workouts` | Listar / Crear rutinas |
| `GET/POST` | `/api/memberships` | Listar / Crear membresías |

---

## 🧪 Tests

```bash
# Ejecutar todos los tests
php artisan test

# Solo tests de la API de clientes
php artisan test --filter=ClientApiTest
```

Los tests cubren permisos por rol, creación de recursos y validación de estados de membresía.

---

## 📧 Notificaciones de membresía

```bash
# Notifica a clientes cuya membresía vence en los próximos 7 días
php artisan gym:notify-expiring

# Cambiar el umbral de días
php artisan gym:notify-expiring --days=14
```

Para automatizarlo, añade esto en `app/Console/Kernel.php`:
```php
$schedule->command('gym:notify-expiring')->dailyAt('09:00');
```

---

## 📁 Estructura del proyecto

```
gymmanager/
├── app/
│   ├── Console/Commands/         # NotifyExpiringMemberships
│   ├── Http/Controllers/         # Controladores web
│   │   └── Api/                  # Controladores API REST
│   ├── Models/                   # User, Client, Trainer, Membership, Workout
│   ├── Notifications/            # MembershipExpiringSoon
│   └── Policies/                 # ClientPolicy, WorkoutPolicy
├── database/
│   ├── migrations/               # 5 migraciones ordenadas
│   └── seeders/                  # Datos realistas de prueba
├── resources/views/              # Vistas Blade
│   ├── layouts/                  # Layout principal con sidebar
│   ├── auth/                     # Login
│   ├── dashboard/
│   ├── clients/
│   ├── trainers/
│   ├── workouts/
│   └── memberships/
└── routes/
    ├── web.php                   # Rutas de la aplicación web
    └── api.php                   # Rutas de la API REST
```

---

## 🎯 Persona objetivo

Este proyecto está orientado a demostrar competencias en **desarrollo backend con Laravel** a:

- **Recruiters técnicos** que buscan desarrolladores PHP/Laravel Junior o Mid
- **CTOs de startups** que necesitan alguien capaz de construir sistemas CRUD completos con autenticación y roles
- **Equipos de producto** que valoran código limpio, estructurado y testeable

---

## 👤 Autor

Hecho con 💪 por **Marc Santolaya Sánchez**

[![GitHub](https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/marcSantolayaSanchez)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/marc-santolaya-sánchez-a80ab2297)
[![Portfolio](https://img.shields.io/badge/Portfolio-C8FF00?style=for-the-badge&logo=googlechrome&logoColor=black)](https://tu-portfolio.com)
