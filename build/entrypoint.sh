#!/bin/bash
set -e

directories="${SYMFONY_CACHE_DIR} ${SYMFONY_LOG_DIR} web/bundles web/compiled web/uploads"

for directory in ${directories}; do
    mkdir -p ${directory}
    chown -R apache:apache ${directory}
done

composer dump-autoload --optimize --no-dev --classmap-authoritative
php bin/console doctrine:models -v --env=${SYMFONY_ENVIRONMENT}
php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}
php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force
php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

for directory in ${directories}; do
    chown -R apache:apache ${directory}
done

id

exec "$@"
