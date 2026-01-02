# License Manager - Sistema de Gestión de Licencias

Sistema de gestión de licencias desarrollado en Laravel para administrar y verificar licencias de plugins premium.

## Requisitos

- PHP 8.1 o superior
- Composer
- MySQL/MariaDB
- Node.js y NPM

## Instalación

### 1. Configurar la base de datos

Crear una base de datos MySQL:

```sql
CREATE DATABASE license_manager;
```

### 2. Configurar variables de entorno

El archivo `.env` ya está configurado con los valores predeterminados:

```env
APP_NAME="License Manager"
APP_URL=https://api.synsighthub.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=license_manager
DB_USERNAME=root
DB_PASSWORD=
```

Actualiza `DB_USERNAME` y `DB_PASSWORD` con tus credenciales de MySQL.

### 3. Ejecutar las migraciones

```bash
php artisan migrate
```

### 4. Crear un usuario administrador

```bash
php artisan tinker
```

Luego ejecuta:

```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@synsighthub.com',
    'password' => bcrypt('password')
]);
```

## Uso del Sistema

### Panel de Administración

1. Accede a `https://api.synsighthub.com/login`
2. Inicia sesión con las credenciales del administrador
3. Gestiona las licencias desde el panel

### Funcionalidades del Panel

- **Listado de licencias**: Ver todas las licencias con su estado, dominio y fecha de expiración
- **Crear licencia**: Generar nuevas licencias con clave única
- **Editar licencia**: Modificar datos de licencias existentes
- **Eliminar licencia**: Borrar licencias del sistema
- **Monitoreo**: Ver última verificación de cada licencia

### API de Verificación de Licencias

**Endpoint**: `POST https://api.synsighthub.com/api/licenses/verify`

**Parámetros**:
```json
{
    "key": "clave-de-licencia",
    "domain": "ejemplo.com"
}
```

**Respuesta Exitosa** (200):
```json
{
    "success": true,
    "message": "Licencia válida.",
    "data": {
        "valid": true,
        "license": {
            "name": "Nombre de la licencia",
            "duration": 12
        },
        "expiration_date": "2027-01-02",
        "remaining_days": 365
    }
}
```

**Respuesta de Error - Licencia no encontrada** (404):
```json
{
    "success": false,
    "message": "Licencia no encontrada o dominio no coincide.",
    "data": {
        "valid": false
    }
}
```

**Respuesta de Error - Licencia expirada** (403):
```json
{
    "success": false,
    "message": "La licencia ha expirado.",
    "data": {
        "valid": false,
        "expired": true,
        "expiration_date": "2025-01-02"
    }
}
```

## Integración con WordPress

El sistema está diseñado para funcionar con el archivo `license-management.php` incluido en el plugin de WordPress.

El plugin WordPress debe hacer una petición POST a:
```
https://api.synsighthub.com/api/licenses/verify
```

Con los datos:
```php
$args = array(
    'key' => $license_key,
    'domain' => $domain,
);
```

## Estructura del Proyecto

```
license-manager/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Api/
│   │       │   └── LicenseVerificationController.php
│   │       └── LicenseController.php
│   └── Models/
│       └── License.php
├── database/
│   └── migrations/
│       └── xxxx_create_licenses_table.php
├── resources/
│   └── views/
│       └── licenses/
│           ├── index.blade.php
│           ├── create.blade.php
│           └── edit.blade.php
└── routes/
    ├── web.php
    └── api.php
```

## Campos de la Licencia

- **license_key**: Clave única de la licencia
- **name**: Nombre descriptivo de la licencia
- **domain**: Dominio autorizado para usar la licencia
- **duration**: Duración en meses
- **expiration_date**: Fecha de expiración
- **is_active**: Estado de la licencia (activa/inactiva)
- **last_checked_at**: Última vez que se verificó la licencia

## Deployment a Producción

### 1. Optimizar la aplicación

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### 2. Configurar permisos

```bash
chmod -R 755 storage bootstrap/cache
```

### 3. Configurar servidor web

Apuntar el dominio `api.synsighthub.com` al directorio `public/` del proyecto.

**Ejemplo de configuración Apache (.htaccess)**:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Ejemplo de configuración Nginx**:
```nginx
server {
    listen 80;
    server_name api.synsighthub.com;
    root /path/to/license-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. Configurar SSL/HTTPS

Se recomienda usar Let's Encrypt para obtener un certificado SSL gratuito:

```bash
certbot --nginx -d api.synsighthub.com
```

## Seguridad

- Todas las rutas del panel están protegidas con autenticación
- Las contraseñas se almacenan encriptadas con bcrypt
- El API de verificación no requiere autenticación para facilitar la integración
- Se recomienda implementar rate limiting en producción

## Soporte

Para cualquier problema o pregunta, contacta al equipo de desarrollo.
