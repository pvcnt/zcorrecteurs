#!/bin/bash
set -e

chown -R www-data:www-data /var/log/symfony
chown -R www-data:www-data /var/cache/symfony
chown -R www-data:www-data /var/cache/composer

mkdir -p web/compiled && chown -R www-data:www-data web/compiled
mkdir -p web/uploads && chown -R www-data:www-data web/uploads
mkdir -p data/index && chown -R www-data:www-data data/index
chown -R www-data:www-data vendor

if [ ! -f app/config/constants.yml ]; then
  gosu www-data cp app/config/constants.sample.yml app/config/constants.yml
fi

gosu www-data composer install --no-dev --no-progress --no-scripts --optimize-autoloader
gosu www-data php app/console doctrine:models -v
gosu www-data php app/console doctrine:migrations:execute  -v --force
gosu www-data php app/console cache:clear  -v
gosu www-data php app/console assets:install web  -v

exec "$@"