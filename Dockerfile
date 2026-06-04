FROM php:8.2-apache

# 1. Install required system packages and Node.js (for Vite/Tailwind)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions for Laravel & PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# 3. Enable Apache URL rewriting (required for Laravel routes)
RUN a2enmod rewrite

# 4. Tell Apache to serve the Laravel /public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Copy your local code into the server
WORKDIR /var/www/html
COPY . .

# 6. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Install dependencies and compile frontend assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# 8. Set file permissions so Laravel can write to storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. When the server boots, migrate the DB, link storage, and start Apache
CMD bash -c "php artisan migrate --force && php artisan storage:link && apache2-foreground"