# Tienda Online GNA

Aplicación de comercio electrónico desarrollada con **Laravel 12** y **Laravel Breeze**. Incluye catálogo público, carrito y checkout, panel de cliente, panel de administración, cotizaciones B2B, inventario con historial y pagos por **Recurrente** (tarjeta), transferencia con boleta y contra entrega.

Moneda principal: **quetzales (GTQ)**.

---

## Contenido

- [Requisitos](#requisitos)
- [Instalación rápida](#instalación-rápida)
- [Variables de entorno](#variables-de-entorno)
- [Base de datos y seeders](#base-de-datos-y-seeders)
- [Ejecutar en desarrollo](#ejecutar-en-desarrollo)
- [Pruebas automatizadas](#pruebas-automatizadas)
- [Rutas principales](#rutas-principales)
- [Módulos del sistema](#módulos-del-sistema)
- [Integraciones](#integraciones)
- [Tareas programadas](#tareas-programadas)
- [Estructura del proyecto](#estructura-del-proyecto)

---

## Requisitos

| Herramienta | Versión recomendada |
|-------------|---------------------|
| PHP | 8.2+ (extensiones: `pdo_mysql` o `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`) |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.x (producción / desarrollo local habitual) |

Para desarrollo solo con SQLite, el `.env.example` trae `DB_CONNECTION=sqlite`; en producción se usa MySQL (`tb_*`).

---

## Instalación rápida

```bash
# 1. Dependencias PHP
composer install

# 2. Entorno
cp .env.example .env
php artisan key:generate

# 3. Base de datos (MySQL: crear la BD y ajustar .env antes)
php artisan migrate

# 4. Datos de demostración (catálogo, usuarios, inventario, etc.)
php artisan db:seed

# 5. Enlace de almacenamiento público (imágenes de productos, PDFs de cotización)
php artisan storage:link

# 6. Frontend (Breeze / Vite)
npm install
npm run build
```

Atajo con el script de Composer (migrate + build incluidos):

```bash
composer setup
```

---

## Variables de entorno

Copia `.env.example` a `.env` y configura al menos:

### Aplicación

| Variable | Descripción |
|----------|-------------|
| `APP_NAME` | Nombre visible de la tienda |
| `APP_URL` | URL base (ej. `http://127.0.0.1:8000`) |
| `APP_DEBUG` | `true` en local, `false` en producción |
| `APP_TIMEZONE` | Zona horaria de la app (`America/Guatemala`) |

### Base de datos (MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tiendaonline_gnacore
DB_USERNAME=root
DB_PASSWORD=
DB_TIMEZONE=-06:00
```

Las fechas de pedidos, historial e inventario usan la hora de Guatemala. Tras cambiar la zona en un entorno ya desplegado, ejecuta `php artisan config:clear`. Los registros creados antes con UTC pueden mostrarse con desfase hasta que se migren o se acepte el histórico tal cual.

### Correo

Necesario para recuperación de contraseña (Breeze). En local puedes usar `MAIL_MAILER=log`.

### Recurrente (pagos con tarjeta)

| Variable | Descripción |
|----------|-------------|
| `RECURRENTE_PUBLIC_KEY` | Clave pública |
| `RECURRENTE_SECRET_KEY` | Clave secreta |
| `RECURRENTE_BASE_URL` | API (por defecto `https://app.recurrente.com/api`) |
| `RECURRENTE_CURRENCY` | `GTQ` |

El webhook `POST /webhooks/recurrente` está excluido de CSRF en `bootstrap/app.php`.

### Envío

| Variable | Descripción |
|----------|-------------|
| `SHIPPING_FREE_THRESHOLD` | Monto mínimo para envío gratis (Q) |
| `SHIPPING_COST` | Costo de envío si no aplica gratis (Q) |

### WhatsApp (Twilio, opcional)

| Variable | Descripción |
|----------|-------------|
| `TWILIO_SID` | Account SID |
| `TWILIO_AUTH_TOKEN` | Auth token |
| `TWILIO_WHATSAPP_NUMBER` | Número remitente (formato WhatsApp) |
| `TWILIO_WHATSAPP_ENABLED` | `true` para enviar mensajes reales |
| `TWILIO_WHATSAPP_COUNTRY_CODE` | `502` (Guatemala) |

Con `TWILIO_WHATSAPP_ENABLED=false` la app funciona sin enviar WhatsApp.

---

## Base de datos y seeders

El modelo de autenticación es **`Usuario`** (`tb_usuario`), no el `User` por defecto de Breeze.

```bash
php artisan migrate
php artisan db:seed
```

Seeders principales (orden en `DatabaseSeeder`): roles, usuarios, estatus, catálogo, geo, **movimientos de inventario**, inventario, carritos, pedidos de ejemplo, etc.

### Usuario administrador de prueba

| Campo | Valor |
|-------|--------|
| Correo | `administrador@example.com` |
| Contraseña | `password` |
| Rol | Administrador (`Id_Rol = 1`) |

Los usuarios de prueba adicionales del seeder también usan contraseña `password`.

### Catálogo de movimientos de inventario

El seeder `MovimientoSeeder` carga tipos como *Reserva por pedido*, *Salida por pedido*, *Ajuste manual*, etc. Son necesarios para el checkout y el historial de inventario.

---

## Ejecutar en desarrollo

Servidor, cola, logs y Vite en paralelo:

```bash
composer dev
```

O por separado:

```bash
php artisan serve
php artisan queue:listen
npm run dev
```

En **entorno local** existe la ruta de prueba `GET /test-whatsapp` (solo si `APP_ENV=local`).

Tras cambiar vistas con Vite, si no usas `npm run dev`, ejecuta `npm run build` para generar `public/build/manifest.json`.

---

## Pruebas automatizadas

Los tests usan **SQLite en memoria** (`phpunit.xml`).

```bash
composer test
# o
php artisan test
```

Cobertura actual: autenticación, flujo de cotizaciones, CRUD de marcas (admin), modelo `Movimiento`.

---

## Rutas principales

| Área | URL | Middleware |
|------|-----|------------|
| Inicio | `/` | — |
| Tienda | `/shop` | — |
| Carrito | `/cart` | — |
| Checkout | `/cart/checkout` | `auth` |
| Dashboard cliente (perfil, pedidos, etc.) | `/dashboard` | `auth`, `usuario` |
| Panel admin | `/admin/dashboard` | `auth`, `usuario`, `admin` |
| Login / registro | `/login`, `/register` | `guest` |

Producto (detalle): `/{id}/{slug}`.

---

## Módulos del sistema

### Tienda pública

- Catálogo, búsqueda y ficha de producto con reseñas.
- Lista de deseos.
- Carrito (sincronización e ítems vía API autenticada).

### Checkout y pedidos

- Validación de stock y **reserva de inventario** al confirmar pedido.
- Métodos: tarjeta (Recurrente), transferencia (boleta de pago), contra entrega.
- Seguimiento y cancelación desde el dashboard del cliente.

### Cotizaciones (B2B)

- El cliente solicita cotización desde el dashboard.
- El admin revisa, emite PDF y define vigencia.
- El cliente acepta o rechaza; las emitidas vencen según plazo configurado.

Configuración de negocio: `config/cotizacion.php` (vigencia, términos, datos del emisor).

### Panel de administración

- Dashboard con KPIs (ingresos, pedidos, productos, usuarios).
- Productos, categorías, **marcas**, inventario (ajustes, historial, ventas).
- Pedidos (seguimiento, historial, cancelación).
- Boletas de pago (aprobar / rechazar transferencias).
- Cotizaciones pendientes y emitidas.
- Departamentos, municipios y usuarios (listado + cambio de rol).

### Inventario

- Stock, stock reservado e historial por movimiento.
- Integrado con pedidos (`InventarioPedidoService`).
- Configuración: `config/inventario.php`.

---

## Integraciones

### Recurrente

Checkout de tarjeta en pestaña externa; confirmación de pago vía **webhook** (`RecurrenteWebhookController`).

### Twilio / WhatsApp

Notificaciones opcionales (reset de contraseña, cambio de contraseña, etc.) controladas por `WhatsAppService` y `config/services.php`.

---

## Tareas programadas

Marcar cotizaciones emitidas como vencidas cuando expire el plazo:

```bash
php artisan cotizaciones:marcar-vencidas
```

El comando está definido en `routes/console.php`. Para automatizarlo en producción, regístralo en el scheduler de Laravel (`bootstrap/app.php` → `withSchedule`) y añade al cron del servidor:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## Middleware

| Alias | Clase | Función |
|-------|--------|---------|
| `auth` | Laravel | Exige sesión iniciada |
| `usuario` | `EnsureAuthenticatedUsuario` | Confirma que el usuario autenticado es `App\Models\Usuario` |
| `admin` | `EnsureUserIsAdmin` | Solo rol administrador (`Id_Rol = 1`) |
| `guest` | Laravel | Bloquea login/registro si ya hay sesión |

**Grupos en rutas:**

- **Cliente:** `auth` + `usuario` (dashboard, carrito, checkout, cotizaciones).
- **Admin:** `auth` + `usuario` + `admin` (prefijo `/admin`).

Tras iniciar sesión, administradores van a `/admin/dashboard` y clientes a `/dashboard`.

---

## Estructura del proyecto

```
app/
├── Http/Controllers/       # Tienda, dashboard, Auth (Breeze)
│   └── Admin/              # Panel administrativo
├── Http/Middleware/        # EnsureAuthenticatedUsuario, EnsureUserIsAdmin
├── Http/Requests/          # Validación por módulo
├── Models/                 # Eloquent (tb_*)
├── Services/               # Lógica de negocio
├── Support/EstatusCatalog.php
└── View/Composers/         # KPIs del layout admin

config/
├── auth.php                # Provider: Usuario
├── cotizacion.php
├── inventario.php
└── shipping.php

database/migrations/        # Esquema tb_*
database/seeders/
resources/views/            # Blade (shop, cart, dashboard, admin)
routes/web.php
routes/auth.php
tests/                      # PHPUnit (Feature + Unit)
```

Convenciones:

- Clave primaria personalizada en modelos (`Id_Producto`, `Id_Usuario`, …).
- Estados de negocio centralizados en `EstatusCatalog` y `EstatusSeeder`.
- Roles: `Usuario::ROL_ADMIN` (1), `Usuario::ROL_USUARIO` (2).

---

## Licencia

Proyecto académico / uso interno GNA. Ajusta la licencia según tu institución o empresa.
