#!/bin/bash
set -e

# Create logs and cache directories.
# These directories are outside of the source code root to avoid polluting the associated volume.
mkdir -p ${SYMFONY_LOG_DIR} && mkdir -p ${SYMFONY_CACHE_DIR} && mkdir -p ${COMPOSER_CACHE_DIR}

# Create web directories.
mkdir -p web/bundles
mkdir -p web/compiled
mkdir -p web/uploads

chown -R apache:apache .

# Create an optimized autoloader (it may not be optimized otherwise) and run Symfony scripts.
composer dump-autoload --optimize
composer run-script symfony-scripts

# Generate models and run database migrations.
php bin/console doctrine:models -v --env=${SYMFONY_ENVIRONMENT}
php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force

# Clear cache.
php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

# Install web assets.
php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}

chown -R apache:apache ${COMPOSER_CACHE_DIR}
chown -R apache:apache ${SYMFONY_CACHE_DIR}
chown -R apache:apache ${SYMFONY_LOG_DIR}
chown -R apache:apache .

exec "$@"
