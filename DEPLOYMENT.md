# Pasos para Desplegar en Servidor de Producción

## 1. Subir archivos al servidor

Sube todos los archivos del proyecto a tu servidor en la ubicación deseada, por ejemplo:
```
/var/www/api.synsighthub.com/
```

## 2. Instalar dependencias

En el servidor, navega a la carpeta del proyecto y ejecuta:

```bash
cd /var/www/api.synsighthub.com
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

## 3. Configurar permisos

```bash
sudo chown -R www-data:www-data /var/www/api.synsighthub.com
sudo chmod -R 755 /var/www/api.synsighthub.com
sudo chmod -R 775 /var/www/api.synsighthub.com/storage
sudo chmod -R 775 /var/www/api.synsighthub.com/bootstrap/cache
```

## 4. Configurar variables de entorno

Edita el archivo `.env` con los datos de producción:

```bash
nano .env
```

Asegúrate de configurar:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://api.synsighthub.com`
- Credenciales de base de datos correctas

## 5. Crear base de datos

En MySQL/MariaDB:

```sql
CREATE DATABASE license_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'license_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON license_manager.* TO 'license_user'@'localhost';
FLUSH PRIVILEGES;
```

## 6. Ejecutar migraciones

```bash
php artisan migrate --force
```

## 7. Crear usuario administrador

```bash
php artisan tinker
```

Ejecuta:
```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'tu-email@synsighthub.com',
    'password' => bcrypt('contraseña-segura')
]);
exit
```

## 8. Optimizar aplicación

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 9. Configurar Virtual Host (Apache)

Crea el archivo de configuración:

```bash
sudo nano /etc/apache2/sites-available/api.synsighthub.com.conf
```

Contenido:

```apache
<VirtualHost *:80>
    ServerName api.synsighthub.com
    ServerAdmin admin@synsighthub.com
    DocumentRoot /var/www/api.synsighthub.com/public

    <Directory /var/www/api.synsighthub.com/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/api.synsighthub.com-error.log
    CustomLog ${APACHE_LOG_DIR}/api.synsighthub.com-access.log combined
</VirtualHost>
```

Habilitar el sitio:

```bash
sudo a2ensite api.synsighthub.com.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## 10. Configurar SSL con Let's Encrypt

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d api.synsighthub.com
```

Selecciona la opción para redirigir todo el tráfico HTTP a HTTPS.

## 11. Configurar Firewall (opcional)

```bash
sudo ufw allow 'Apache Full'
sudo ufw enable
```

## 12. Configurar Cron Job para Tareas Programadas (opcional)

Si necesitas ejecutar tareas programadas de Laravel:

```bash
crontab -e
```

Añade:
```
* * * * * cd /var/www/api.synsighthub.com && php artisan schedule:run >> /dev/null 2>&1
```

## 13. Verificar instalación

Visita `https://api.synsighthub.com/login` para verificar que todo funciona correctamente.

## Troubleshooting

### Error 500

Verifica los permisos de las carpetas `storage` y `bootstrap/cache`:

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error de conexión a base de datos

Verifica las credenciales en `.env` y que el usuario de MySQL tenga los permisos correctos.

### Páginas en blanco

Revisa los logs:

```bash
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/api.synsighthub.com-error.log
```

## Mantenimiento

### Actualizar la aplicación

```bash
cd /var/www/api.synsighthub.com
git pull origin main  # Si usas Git
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup de base de datos

```bash
mysqldump -u license_user -p license_manager > backup_$(date +%Y%m%d).sql
```

### Limpiar caché

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
