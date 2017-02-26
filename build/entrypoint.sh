#!/bin/bash
set -e

chown -R www-data:www-data /var/log/symfony
chown -R www-data:www-data /var/cache/symfony
chown -R www-data:www-data /var/cache/composer

chown -R www-data:www-data . > /dev/null || true

if [ ! -f app/config/constants.yml ]; then
  gosu www-data cp app/config/constants.sample.yml app/config/constants.yml
fi

gosu www-data composer install --no-dev --no-progress --no-scripts --optimize-autoloader
gosu www-data php app/console doctrine:models -v
gosu www-data php app/console doctrine:migrations:execute  -v --force
gosu www-data php app/console cache:clear  -v
gosu www-data php app/console assets:install web  -v

exec "$@"