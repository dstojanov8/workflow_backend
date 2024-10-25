# Use official PHP image with necessary extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install required PHP extensions and system dependencies
RUN docker-php-ext-install pdo pdo_mysql

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copy only composer.json and composer.lock first
# COPY ../composer.json /var/www/html/composer.json
# COPY ../composer.lock /var/www/html/composer.lock

# # Install PHP dependencies using Composer: --no-scripts --no-autoloader => used for for optimization purposes
# RUN composer install --no-scripts --no-autoloader

# Copy the application code to the container
COPY ../ /var/www/html

# Generate the autoload files after copying dependencies: optimized autoload files for performance.
# RUN composer dump-autoload --optimize


# Set proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]

