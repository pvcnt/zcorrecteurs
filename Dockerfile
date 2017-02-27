FROM php:7.1-apache
MAINTAINER Vincent <vincent@zcorrecteurs.fr>

RUN set -x \
  && DEBIAN_FRONTEND=noninteractive apt-get update \
  && apt-get install -y --no-install-recommends \
    ca-certificates \
    wget \
    wdiff \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    zlib1g-dev \
  && rm -rf /var/lib/apt/lists/*

# Install gosu binary (needs wget and ca-certificates).
ENV GOSU_VERSION 1.9
RUN set -x \
  && dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')" \
  && wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch" \
  && wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc" \
  && export GNUPGHOME="$(mktemp -d)" \
  && gpg --keyserver ha.pool.sks-keyservers.net --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4 \
  && gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu \
  && rm -r "$GNUPGHOME" /usr/local/bin/gosu.asc \
  && chmod +x /usr/local/bin/gosu \
  && gosu nobody true

# Install composer binary (needs wget).
RUN wget -O composer-setup.php https://getcomposer.org/installer \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php \
  && mkdir -p /var/cache/composer

# Install PHP extensions: iconv, mcrypt, gd, pdo, pdo_mysql, zip
RUN docker-php-ext-install -j$(nproc) iconv mcrypt pdo pdo_mysql zip opcache \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd \
  && pecl install apcu-5.1.5 \
  && docker-php-ext-enable apcu

# Add and enable our Symfony website.
COPY build/symfony.conf /etc/apache2/sites-available/
RUN service apache2 stop \
  && a2enmod rewrite \
  && a2dissite 000-default \
  && a2ensite symfony

# Create logs and cache directories.
# These directories are outside of the source code root to avoid polutting the associated volume.
RUN mkdir -p /var/log/symfony \
  && mkdir -p /var/cache/symfony \
  && mkdir -p /var/cache/composer \
  && chown -R www-data:www-data /var/log/symfony \
  && chown -R www-data:www-data /var/cache/symfony \
  && chown -R www-data:www-data /var/cache/composer
ENV COMPOSER_CACHE_DIR /var/cache/composer
VOLUME /var/log/symfony
VOLUME /var/cache/symfony

# Add a custom entrypoint.
COPY build/entrypoint.sh /
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]
EXPOSE 80
ENV ENVIRONMENT prod
ENV DEBUG false

# First download PHP dependencies.
RUN mkdir -p /opt/app && chown www-data:www-data /opt/app
WORKDIR /opt/app
COPY composer.json composer.lock /opt/app/
RUN gosu www-data composer install --no-dev --no-progress --no-scripts --no-autoloader

# Then add source code.
COPY . /opt/app