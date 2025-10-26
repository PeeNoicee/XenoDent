# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies and build assets
RUN npm ci && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Laravel optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create startup script
RUN echo '#!/bin/bash\n\
echo "Database connection info:"\n\
echo "DB_HOST: $DB_HOST"\n\
echo "DB_PORT: $DB_PORT"\n\
echo "DB_DATABASE: $DB_DATABASE"\n\
echo "DB_CONNECTION: $DB_CONNECTION"\n\
echo "Creating storage symlink..."\n\
php artisan storage:link\n\
echo "Caching Laravel configuration..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
echo "Waiting for database to be ready..."\n\
for i in {1..30}; do\n\
  if php artisan tinker --execute="DB::connection()->getPdo(); echo \\"Database connected successfully\\";" 2>/dev/null; then\n\
    echo "Database is ready!"\n\
    break\n\
  fi\n\
  echo "Waiting for database... ($i/30)"\n\
  sleep 2\ndone\n\
php artisan migrate --force\n\
apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Expose port
EXPOSE 80

# Start with migrations and Apache
CMD ["/usr/local/bin/start.sh"]
