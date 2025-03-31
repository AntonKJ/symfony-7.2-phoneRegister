#  symfony-7.2-phoneRegister

Установка выполнить docker-compose up -d из первой (корневой)

http://0.0.0.0/api/login
http://0.0.0.0:15432/ - pgAdmin
```
Все подключения в .env
```
Таблицы в bd_tables данные в csv
```
docker exec -it Kobezev-A-symfony-phone-register-php su
cd /home/phoneRegister/symfony/phoneRegister
composer install
```

#==================#END#==================#
#  ERRORS
#if not PDO Driver
```
docker exec -it php su
docker-php-ext-install pdo_pgsql
```

       /usr/local/bin/docker-php-ext-install pdo pdo_pgsql
       /usr/local/bin/docker-php-ext-install -j5 gd mbstring mysqli pdo pdo_pgsql shmop

#or
```
docker-compose exec php docker-php-ext-install pdo_pgsql
docker-compose exec php docker-php-ext-install intl
```
```
docker-compose restart
```
#if not ZIP extension
```
set -eux     
&& apt-get update     
&& apt-get install -y libzip-dev zlib1g-dev     
&& docker-php-ext-install zip
```
```
docker-compose restart
```
#if excepsion Message format 'date' is not supported. You have to install PHP intl extension to use this feature.
```
docker exec -it php su
apt-get -y update \
    && apt-get install -y libicu-dev\
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl
```
#or
```
docker exec -it php su
docker-php-ext-install intl
```
#if gd not available
```
docker exec -it php su

apt-get update && \
    apt-get install -y \
        zlib1g-dev libpng-dev\
    && docker-php-ext-install gd
```
RESTART DOCKER AFTER INSTALL EXTS!


