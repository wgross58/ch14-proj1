# Use PHP with Apache web server
FROM php:8.2-apache


# Copy your project files into the Apache root folder
COPY . /var/www/html

# (Optional) Fix file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 (so Render knows where your app is running)
EXPOSE 80