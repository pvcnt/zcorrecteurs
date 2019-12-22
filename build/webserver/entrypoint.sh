#!/bin/bash
set -e

# Some directories do need to exist and have the correct permissions.
directories="${SYMFONY_CACHE_DIR} ${SYMFONY_LOG_DIR} web/bundles web/compiled"
for directory in ${directories}; do
    mkdir -p ${directory}
done

php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}
#php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force
php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

for directory in ${directories}; do
    chown -R apache:apache ${directory}
done

exec "$@"
