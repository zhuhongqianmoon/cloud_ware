# 使用官方 PHP-Apache 镜像
FROM daocloud.io/php:5.6-apache 


RUN apt-get update && \
    apt-get install -y \
        libmcrypt-dev
# docker-php-ext-install 为官方 PHP 镜像内置命令，用于安装 PHP 扩展依赖
# pdo_mysql 为 PHP 连接 MySQL 扩展
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mcrypt
RUN a2enmod rewrite

# /var/www/html/ 为 Apache 目录
COPY . /var/www/html/
RUN  chmod -R 777 /var/www/html/*