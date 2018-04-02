FROM php:7.2-apache
MAINTAINER Vincent <vincent@zcorrecteurs.fr>

RUN set -x \
  && DEBIAN_FRONTEND=noninteractive apt-get update \
  && apt-get install -y --no-install-recommends \
    ca-certificates \
    wget \
    wdiff \
    git \
    gnupg2 \
    dirmngr \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libxslt1-dev \
  && rm -rf /var/lib/apt/lists/*

# Install gosu binary (needs wget and ca-certificates).
ENV GOSU_VERSION 1.10
RUN set -x \
  && dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')" \
  && wget -nv -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch" \
  && wget -nv -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc" \
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

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) iconv pdo pdo_mysql zip opcache xml xsl \
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

# Add a custom entrypoint.
COPY build/entrypoint.sh /
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]

ENV COMPOSER_CACHE_DIR=/var/cache/composer \
  SYMFONY_LOG_DIR=/var/log/symfony \
  SYMFONY_CACHE_DIR=/var/cache/symfony \
  SYMFONY_ENVIRONMENT=prod \
  SYMFONY_DEBUG=false

WORKDIR /opt/app
COPY . /opt/app