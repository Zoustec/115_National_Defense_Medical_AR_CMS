FROM node:20-slim AS node
WORKDIR /tmp

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

FROM php:8.4-apache

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
        cron \
        gnupg \
        libbz2-dev \
        libfreetype6-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        redis-server \
        supervisor \
        vim \
        wget \
        zip && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    docker-php-ext-install pdo pdo_mysql mysqli && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-install xml && \
    rm -rf /var/lib/apt/lists/* && \
    chown -R www-data:www-data /etc/redis/ && \
    yes no | pecl install redis && \
    docker-php-ext-enable redis && \
    a2enmod rewrite && \
    chmod 1777 /var/run/ && \
    chmod u+s /usr/sbin/cron && \
    echo "* * * * * /usr/local/bin/php /var/www/html/artisan schedule:run 2>&1" | crontab -u www-data - && \
    chmod u+x /var/spool/cron/crontabs/www-data && \
    sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf && \
    echo "output_buffering=4096" > /usr/local/etc/php/conf.d/session.ini && \
    printf "upload_max_filesize = 100M\npost_max_size = 100M\nclient_max_body_size = 100M\nmemory_limit = 2048M\n" \
        > /usr/local/etc/php/conf.d/uploads.ini && \
    a2enmod remoteip
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html

USER www-data

COPY --from=node --chown=www-data:www-data /tmp/public/build/ public/build/
COPY --chown=www-data:www-data . .

# ARG ENV_FILE=.env.example
# COPY --chown=www-data:www-data ${ENV_FILE} .env

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
RUN mkdir -p bootstrap/cache && \
    chown -R www-data:www-data bootstrap/cache storage && \
    chmod -R 775 bootstrap/cache storage && \
    composer install --ignore-platform-reqs --prefer-dist

# Create .env from example if not exists, then generate key
# RUN cp -n .env.example .env 2>/dev/null || true && \
# php artisan key:generate

# Copy Supervisor configuration and entrypoint
USER root
COPY supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /entrypoint.sh
RUN mkdir -p /var/log/supervisor /tmp && \
    chown -R www-data:www-data /var/log/supervisor /tmp && \
    chmod +x /entrypoint.sh && \
    chmod 777 /tmp && \
    sed -i 's/\r$//' /entrypoint.sh

# Switch back to www-data for security
USER www-data

EXPOSE 8080

ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]
