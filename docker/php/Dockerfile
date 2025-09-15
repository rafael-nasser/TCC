# Usar PHP 8.x com Apache
FROM php:8.2-apache

# Instala extensões necessárias para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilita módulos do Apache
RUN a2enmod rewrite headers

# Copia configuração de VirtualHost
COPY docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html
