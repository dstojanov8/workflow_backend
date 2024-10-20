# Use official PHP image with necessary extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install required PHP extensions and system dependencies
RUN docker-php-ext-install pdo pdo_mysql

# Copy the application code to the container
COPY ../ /var/www/html

# Copy the app to the container (if Dockerfile is in root folder)
# COPY . /var/www/html

# Set proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]

