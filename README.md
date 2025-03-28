#  Laravel-vue

!["Dashboard Vue.js Presentation"](https://github.com/AntonKJ/laravel-vue/blob/main/scrin-2022-03-28_19-58.png)

Dashboard template дизайн из Figma (выгрузил местным гениратором кода)

 https://www.figma.com/file/B7lZ78uVTP5xflPYL3Asmt/Dashboard-Advanced?node-id=0%3A1

Установка выполнить docker-compose up -d из первой (корневой)

http://0.0.0.0/home
http://0.0.0.0:8765 - PHPMyAdmin
```
MYSQL_ROOT_PASSWORD: rootpwd6421
```
Установка db mysql из корневой папки проекта, миграции есть 
```
cat laravelvue.sql | docker exec -it lara-vue-mariadb /usr/bin/mysql -u root --password=rootpwd6421 laravelvue
```
```
docker exec -it laravel-vue-app su
cd home/LaravelVue/
composer install
```
код проекта в src/LaravelVue/

Корневая дирректория проекта
 
https://github.com/AntonKJ/laravel-vue/tree/main/src/LaravelVue

выполнить в контейнере

#==================#END#==================#
#  ERRORS
#if not PDO Driver
```
docker exec -it php su
docker-php-ext-install pdo_mysql
```

       /usr/local/bin/docker-php-ext-install pdo pdo_mysql
       /usr/local/bin/docker-php-ext-install -j5 gd mbstring mysqli pdo pdo_mysql shmop

#or
```
docker-compose exec php docker-php-ext-install pdo_mysql
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
