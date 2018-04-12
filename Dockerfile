FROM php:7-apache

COPY emailusers.php /var/www/html/
ENV PWP_SCRIPTNAME emailusers.php

ADD http://www.mariovaldez.net/software/phpwpoison/files/pwpwords.tar.gz /var/www/html/
RUN \
  tar xvf pwpwords.tar.gz
