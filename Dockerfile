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

# Script de entrada para garantizar conectividad localhost/127.0.0.1 y socket hacia el contenedor de base de datos
COPY <<'EOF' /usr/local/bin/docker-php-entrypoint-custom
#!/bin/bash
set -e

# Si el socket no está montado directamente, o para asegurar que 127.0.0.1:3306 apunte a MariaDB/MySQL:
if command -v socat >/dev/null 2>&1; then
    (while true; do socat TCP-LISTEN:3306,fork,reuseaddr TCP:db:3306 2>/dev/null || sleep 2; done) &
fi

exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-php-entrypoint-custom

WORKDIR /var/www/html

ENTRYPOINT ["docker-php-entrypoint-custom"]
