FROM php:7.2-fpm

RUN apt-get update && apt-get install -y --force-yes \
        default-libmysqlclient-dev  \
		libxml2-dev \
		libc-client-dev libkrb5-dev libpng-dev \
		screen \
		git \
	    unoconv \
	    gnupg \

    && docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql mbstring soap zip gd \
    && docker-php-ext-enable mysqli pdo pdo_mysql mbstring soap	zip gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	    && composer --version

RUN rm /etc/localtime \
		    && ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime \
		    && "date"

RUN pecl config-set php_ini /usr/local/etc/php

RUN apt-get -y install \
	    libmagickwand-dev \
	        --no-install-recommends \
	    && pecl install imagick \
	    && docker-php-ext-enable imagick \
	    && rm -r /var/lib/apt/lists/*

RUN pecl install xdebug

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
     && docker-php-ext-install imap

RUN docker-php-ext-enable imap

RUN docker-php-ext-enable xdebug

RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& echo "xdebug.remote_host=172.17.0.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


CMD ["php-fpm"]
