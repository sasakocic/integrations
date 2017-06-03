FROM php

RUN export TERM=linux \
 && export DEBIAN_FRONTEND=noninteractive \
 && apt-get -y -qq -o=Dpkg::Use-Pty=0 update \
 && apt-get install -y -qq -o=Dpkg::Use-Pty=0 git curl unzip apt-utils \
 && apt-get install -y -qq -o=Dpkg::Use-Pty=0 ant default-jdk phing libxslt-dev libcommons-net-java \
 && apt-get install -y -qq -o=Dpkg::Use-Pty=0 phploc pdepend phpcpd phpdox phpunit phpmd php-codesniffer \
 && pecl install -o -f xdebug \
 && docker-php-ext-install -j5 xsl \
 && echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && cd /usr/share/ant/lib/ \
 && ln -s /usr/share/java/commons-net.jar commons-net.jar \
 && ln -s /usr/share/java/oro.jar oro.jar
