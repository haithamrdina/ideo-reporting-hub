FROM php:8.2.3-apache

# Install dependencies and composer
RUN echo 'SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1' >> /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    \
    &&  apt-get update \
    &&  apt-get install -y --no-install-recommends \
    locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip \
    \
    &&  echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
    &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    &&  locale-gen \
    \
    &&  curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer \
    \ 
    &&  docker-php-ext-configure \
    intl \
    &&  docker-php-ext-install \
    pdo pdo_mysql opcache intl zip calendar dom mbstring gd xsl exif; 

# Install required PHP extensions

# Update PEAR and PECL channels
RUN pear update-channels \
    && pecl update-channels

RUN pecl install -o -f apcu \
    && rm -rf /tmp/pear \ 
    && docker-php-ext-enable apcu

RUN docker-php-ext-install pcntl   \
    && docker-php-ext-enable pcntl 

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \ 
    && docker-php-ext-enable redis

# Install node    
RUN set -uex; \
    apt-get update; \
    apt-get install -y ca-certificates curl gnupg; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
    | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    NODE_MAJOR=18; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" \
    > /etc/apt/sources.list.d/nodesource.list; \
    apt-get update; \
    apt-get install nodejs -y;

# Config TimeZone
RUN apt update && apt install tzdata -y
ENV TZ="Africa/Casablanca"

# Setup dependencies in php.ini
RUN cd /usr/local/etc/php/conf.d/ && echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini
RUN cd /usr/local/etc/php/conf.d/ && echo 'max_input_vars = 10000' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini
RUN cd /usr/local/etc/php/conf.d/ && echo 'upload_max_filesize = 250M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini
RUN cd /usr/local/etc/php/conf.d/ && echo 'post_max_size = 350M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini
RUN cd /usr/local/etc/php/conf.d/ && echo 'max_execution_time = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

WORKDIR /var/www/app

# Install supervisor 
RUN apt-get install -y supervisor

# Install cron 
RUN apt-get update && apt-get -y install cron && apt-get install nano

# Copy Laravel worker configuration
COPY ./workers/laravel-horizon.conf /etc/supervisor/conf.d/laravel-horizon.conf

# Copy Laravel cron job configuration
COPY ./schedulers/laravel-cron /etc/cron.d/laravel-cron

# Set permissions for configuration files

RUN chown www-data:www-data /etc/supervisor/conf.d/laravel-horizon.conf  \
    && chmod 644 /etc/supervisor/conf.d/laravel-horizon.conf  \
    && chown www-data:www-data /etc/cron.d/laravel-cron \
    && chmod 644 /etc/cron.d/laravel-cron 

# apply cron job
RUN crontab /etc/cron.d/laravel-cron

# create log directories and set permissions
RUN mkdir -p /var/www/app/storage/logs \
    && touch /var/www/app/storage/logs/horizon.log \
    && touch /var/www/app/storage/logs/cron.log \
    && chown -R www-data:www-data /var/www/app/storage/logs \
    && chmod 664 /var/www/app/storage/logs/horizon.log \
    && chmod 664 /var/www/app/storage/logs/cron.log

# set working directory
WORKDIR /var/www/app

# start Supervisor and Cron
CMD ["sh", "-c", "apache2-foreground & service supervisor start & cron -f"]