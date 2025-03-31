FROM php:8.2-fpm

# Install modules
RUN apt-get update && apt-get install -y \
	apt-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libicu-dev \
	libzip-dev \
	libonig-dev \
	libpq-dev \
	libpng-dev \
	zlib1g-dev \
        wget \
        git 

RUN docker-php-ext-install pdo pdo_mysql zip exif \
	&& docker-php-ext-configure gd \
	&& docker-php-ext-install gd 


#RUN pecl install -o -f xdebug \
#    && rm -rf /tmp/pear

COPY ./install-composer.sh /

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && sh /install-composer.sh \
    && rm /install-composer.sh

RUN usermod -u 1000 www-data

VOLUME /root/.composer

WORKDIR /home/phoneRegister

EXPOSE 9000
CMD ["php-fpm"]








