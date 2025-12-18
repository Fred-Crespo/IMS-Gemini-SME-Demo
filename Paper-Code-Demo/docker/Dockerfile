# Usamos una imagen base oficial con PHP 8.2 y FPM (manejador de procesos de PHP)
FROM php:8.2-fpm-bookworm

# Variables de entorno para que sea no-interactivo
ENV DEBIAN_FRONTEND=noninteractive

# Instalamos dependencias del sistema y extensiones de PHP que Laravel necesita
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \ 
    zip \
    unzip \
    nginx \
    supervisor

# Instalamos Node.js y npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip curl
# Instalamos Composer (manejador de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo dentro de la caja
WORKDIR /var/www/html

# Copiamos todo el código de tu proyecto (local) DENTRO de la caja
COPY . .

# --- Configuración para Producción (Nginx y Supervisor) ---
# Copiamos la configuración de Nginx al contenedor
COPY docker/nginx.conf /etc/nginx/sites-enabled/default
# Copiamos la configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# Copiamos el script de inicio
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh
# -----------------------------------------------------------

RUN git config --global --add safe.directory /var/www/html

# --- INICIO DE LA CORRECCIÓN ---
# 1. Creamos las carpetas de 'storage' y 'bootstrap/cache' que Laravel necesita
RUN mkdir -p storage/app/public storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

# 2. Damos permisos al servidor web ANTES de que composer se ejecute
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
# --- FIN DE LA CORRECCIÓN ---
    
# Instalamos las dependencias de Laravel (sin las de desarrollo)
RUN composer install --no-interaction --optimize-autoloader

# Instalamos dependencias de frontend y compilamos
RUN npm install
RUN npm run build


# Ajustamos permisos para que el servidor pueda escribir en carpetas clave
#RUN chown -R www-data:www-data storage bootstrap/cache
#RUN chmod -R 775 storage bootstrap/cache


# Exponemos el puerto 9000 para que Nginx pueda hablar con PHP
#EXPOSE 9000

# El comando que se ejecuta para iniciar el servidor PHP
#CMD ["php-fpm"]

# Limpiamos la caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Exponemos el puerto 80, que es donde Nginx escuchará
EXPOSE 80

# El comando que se ejecuta para iniciar TODO
CMD ["/usr/local/bin/start.sh"]