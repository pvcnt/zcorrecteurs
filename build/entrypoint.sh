#!/bin/bash
set -e

# Create logs and cache directories.
# These directories are outside of the source code root to avoid polutting the associated volume.
mkdir -p ${SYMFONY_LOG_DIR} && chown -R www-data:www-data ${SYMFONY_LOG_DIR}
mkdir -p ${SYMFONY_CACHE_DIR} && chown -R www-data:www-data ${SYMFONY_CACHE_DIR}
mkdir -p ${COMPOSER_CACHE_DIR} && chown -R www-data:www-data ${COMPOSER_CACHE_DIR}

# Create web directories.
mkdir -p web/bundles
mkdir -p web/compiled
mkdir -p web/uploads

chown -R www-data:www-data .

# Create an optimized autoloader (it may not be optimized otherwise) and run Symfony scripts.
gosu www-data composer dump-autoload --optimize
gosu www-data composer run-script symfony-scripts

# Generate models and run database migrations.
gosu www-data php bin/console doctrine:models -v --env=${SYMFONY_ENVIRONMENT}
gosu www-data php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force

# Clear cache.
gosu www-data php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

# Install web assets.
gosu www-data php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}

echo "executing $@"
exec "$@"
