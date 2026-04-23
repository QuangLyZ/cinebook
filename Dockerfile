# Base PHP
FROM php:8.2-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Node (version chuẩn, không dùng apt)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Backend
RUN composer install --no-dev --optimize-autoloader

# Frontend
RUN npm install && npm run build

# Laravel optimize (tạm thời disable route cache nếu chưa clean)
RUN php artisan config:cache && php artisan view:cache

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
