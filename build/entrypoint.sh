#!/bin/bash
set -e

# Create logs and cache directories.
# These directories are outside of the source code root to avoid polluting the associated volume.
mkdir -p ${SYMFONY_LOG_DIR} && mkdir -p ${SYMFONY_CACHE_DIR}

# Create web directories.
mkdir -p web/bundles
mkdir -p web/compiled
mkdir -p web/uploads

chown -R apache:apache .

composer dump-autoload --optimize --no-dev --classmap-authoritative
php bin/console doctrine:models -v --env=${SYMFONY_ENVIRONMENT}
php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}
php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force

php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

chown -R apache:apache ${SYMFONY_CACHE_DIR}
chown -R apache:apache ${SYMFONY_LOG_DIR}
chown -R apache:apache .

exec "$@"
