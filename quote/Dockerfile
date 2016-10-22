FROM php:7.0-apache
VOLUME ["/var/www/html/"]
# enable .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
# mod_rewrite on
RUN a2enmod rewrite

# mysql driver
RUN apt-get update
RUN apt-get install -y php5-mysql
RUN docker-php-ext-install  pdo_mysql
