FROM php:8.0-apache
RUN docker-php-ext-install pdo pdo_mysql session
RUN docker-php-ext-enable session
EXPOSE 80