#!/bin/bash
set -e

mkdir -p ${SYMFONY_LOG_DIR} && chown -R www-data:www-data ${SYMFONY_LOG_DIR}
mkdir -p ${SYMFONY_CACHE_DIR} && chown -R www-data:www-data ${SYMFONY_CACHE_DIR}

chown -R www-data:www-data var

mkdir -p web/bundles && chown -R www-data:www-data web/bundles
mkdir -p web/compiled && chown -R www-data:www-data web/compiled
mkdir -p web/uploads && chown -R www-data:www-data web/uploads
mkdir -p data/index && chown -R www-data:www-data data/index

# Only in development environment, we run again `composer install`, because development dependencies where not
# installed in the Dockerfile.
if [ ${SYMFONY_ENVIRONMENT} == "dev" ]; then
  chown -R www-data:www-data ${COMPOSER_CACHE_DIR}
  chown -R www-data:www-data vendor
  gosu www-data composer install --no-progress --optimize-autoloader
fi

gosu www-data php bin/console doctrine:models -v --env=${SYMFONY_ENVIRONMENT}
gosu www-data php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force
gosu www-data php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}
gosu www-data php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}

exec "$@"