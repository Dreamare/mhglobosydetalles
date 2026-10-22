FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PHP requeridas (mysqli, pdo_mysql, curl)
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-client \
    socat \
    libcurl4-openssl-dev \
    && docker-php-ext-install mysqli pdo_mysql curl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configuración de PHP para sockets de MySQL y límites de subida
RUN { \
    echo 'mysqli.default_socket = /var/run/mysqld/mysqld.sock'; \
    echo 'pdo_mysql.default_socket = /var/run/mysqld/mysqld.sock'; \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'memory_limit = 256M'; \
    echo 'display_errors = On'; \
    echo 'error_reporting = E_ALL & ~E_NOTICE & ~E_WARNING'; \
} > /usr/local/etc/php/conf.d/docker-php-custom.ini

# Script de entrada para adaptar el puerto dinámico de Render y garantizar conectividad
COPY <<'EOF' /usr/local/bin/docker-php-entrypoint-custom
#!/bin/bash
set -e

# Adaptar puerto de Apache si Render o el entorno define la variable PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# Conectividad local si se requiere puente
if command -v socat >/dev/null 2>&1; then
    (while true; do socat TCP-LISTEN:3306,fork,reuseaddr TCP:db:3306 2>/dev/null || sleep 2; done) &
fi

exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-php-entrypoint-custom

WORKDIR /var/www/html

# Copiar el código del proyecto dentro de la imagen
COPY . /var/www/html

ENTRYPOINT ["docker-php-entrypoint-custom"]
