FROM library/php:8.1-apache

# Stripdown & Install
RUN apt update && \
    apt -qq -y upgrade && \
    apt-get install -y git zip unzip libzip-dev

# Update php for mysql
RUN docker-php-ext-install zip && docker-php-ext-enable zip
RUN docker-php-ext-install pdo && docker-php-ext-enable pdo
RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

RUN a2enmod rewrite headers

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN useradd -m composer-user
WORKDIR /var/www/html

COPY api/ /var/www/html
RUN chown -R composer-user:composer-user /var/www/html

USER composer-user
RUN composer install --no-scripts --ignore-platform-reqs

USER root
COPY entrypoint.sh /entrypoint.sh
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Give docker web server permission to write to cache
RUN chown -R www-data.www-data var

# Ensure the entrypoint script has execute permissions
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
