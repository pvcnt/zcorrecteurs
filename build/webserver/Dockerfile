# zCorrecteurs.fr is the software behind www.zcorrecteurs.fr
#
# Copyright (C) 2012-2020 Corrigraphie
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

# This image comes with PHP 7.2.
FROM alpine:3.9

ARG COMPOSER_VERSION=1.9.1

RUN apk update && \
  apk upgrade && \
  apk add \
	apache2 \
	openssl \
    openssh \
    tzdata \
    openntpd \
    bash \
    curl \
	php7-apache2 \
	php7 \
	php7-phar \
	php7-json \
	php7-iconv \
	php7-openssl \
	php7-xdebug \
	php7-mbstring \
	php7-dom \
	php7-pdo \
	php7-zip \
	php7-gd \
	php7-pdo_mysql \
	php7-pdo_sqlite \
	php7-curl \
	php7-apcu \
	php7-ctype \
	php7-session \
	php7-simplexml \
	php7-xsl \
	php7-fileinfo \
	php7-memcached \
	php7-tokenizer

RUN cp /usr/bin/php7 /usr/bin/php && rm -f /var/cache/apk/*

RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/cb19f2aa3aeaa2006c0cd69a7ef011eb31463067/web/installer \
    && php -r " \
      \$signature = '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5'; \
      \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
      if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
      }" \
    && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
    && composer --ansi --version --no-interaction \
    && rm -f /tmp/installer.php \
    && find /tmp -type d -exec chmod -v 1777 {} +

RUN mkdir -p /run/apache2 \
    && sed -i "s/^#LoadModule\ rewrite_module/LoadModule\ rewrite_module/" /etc/apache2/httpd.conf \
    && sed -i "s/^#LoadModule\ session_module/LoadModule\ session_module/" /etc/apache2/httpd.conf \
    && sed -i "s/^#LoadModule\ session_cookie_module/LoadModule\ session_cookie_module/" /etc/apache2/httpd.conf \
    && sed -i "s/^#LoadModule\ session_crypto_module/LoadModule\ session_crypto_module/" /etc/apache2/httpd.conf \
    && sed -i "s/^#LoadModule\ deflate_module/LoadModule\ deflate_module/" /etc/apache2/httpd.conf \
    && sed -i "s/^#ServerName\ www.example.com:80/ServerName\ localhost/" /etc/apache2/httpd.conf \
    && sed -i "s/^Listen\ 80/Listen\ 0.0.0.0:80/" /etc/apache2/httpd.conf \
    && sed -i "s#^DocumentRoot \".*#DocumentRoot \"/opt/app/web\"#" /etc/apache2/httpd.conf \
    && printf "\n<Directory \"/opt/app/web\">\n\tAllowOverride All\n\tOptions -Indexes\n\tRequire all granted\n</Directory>\n" >> /etc/apache2/httpd.conf \
    && sed -i "s/^variables_order\ =\ \"GPCS\"/variables_order\ =\ \"EGPCS\"/" /etc/php7/php.ini \
    && sed -i "s/^;realpath_cache_size\ =\ 4096k/realpath_cache_size=4096K/" /etc/php7/php.ini \
    && sed -i "s/^;realpath_cache_ttl\ =\ 120/realpath_cache_ttl=600/" /etc/php7/php.ini \
    #&& sed -i "s/^;opcache.validate_timestamps=1/opcache.validate_timestamps=0/" /etc/php7/php.ini \
    && sed -i "s/^;opcache.memory_consumption=128/opcache.memory_consumption=256/" /etc/php7/php.ini \
    && sed -i "s/^;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/" /etc/php7/php.ini \
    && sed -i "s/^;date.timezone\ =/date.timezone\ =\ \"Europe\/Paris\"/" /etc/php7/php.ini \
    && sed -i "s/^;intl.default_locale\ =/intl.default_locale\ =\ \"fr_FR.UTF-8\"/" /etc/php7/php.ini \
    && ln -sfT /dev/stderr /var/log/apache2/error.log \
    && ln -sfT /dev/stdout /var/log/apache2/access.log

COPY build/webserver/entrypoint.sh /
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["serve"]
EXPOSE 80

ENV SYMFONY_LOG_DIR=/var/log/symfony \
  SYMFONY_CACHE_DIR=/var/cache/symfony \
  SYMFONY_ENVIRONMENT=prod \
  SYMFONY_DEBUG=false

WORKDIR /opt/app

# First copy only the composer files, to avoid re-downloading dependencies
# every time the source code changes.
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --no-plugins \
    --prefer-dist \
    --no-dev \
    --no-autoloader \
    --no-scripts

# Then copy the whole source code, and generate the autoloader.
COPY . .
RUN composer dump-autoload --optimize
