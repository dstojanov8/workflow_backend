# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy the app code to the container
COPY ../ /var/www/html

# Set working directory (if Dockerfile is in root folder)
# WORKDIR /var/www/html

# Copy the app to the container
COPY . /var/www/html

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]



















