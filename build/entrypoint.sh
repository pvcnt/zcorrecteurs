#!/bin/bash
set -e

# usage: file_env VAR [DEFAULT]
#    ie: file_env 'XYZ_DB_PASSWORD' 'example'
# (will allow for "$XYZ_DB_PASSWORD_FILE" to fill in the value of
#  "$XYZ_DB_PASSWORD" from a file, especially for Docker's secrets feature)
# Taken from MySQL Docker image.
file_env() {
	local var="$1"
	local fileVar="${var}_FILE"
	local def="${2:-}"
	if [ "${!var:-}" ] && [ "${!fileVar:-}" ]; then
		echo >&2 "error: both $var and $fileVar are set (but are exclusive)"
		exit 1
	fi
	local val="$def"
	if [ "${!var:-}" ]; then
		val="${!var}"
	elif [ "${!fileVar:-}" ]; then
		val="$(< "${!fileVar}")"
	fi
	export "$var"="$val"
	unset "$fileVar"
}

# Expanded environment variables that may contain a secret.
file_env DATABASE_PASSWORD
file_env APP_SECRET
file_env SENDGRID_APIKEY

# Some directories do need to exist and have the correct permissions.
directories="${SYMFONY_CACHE_DIR} ${SYMFONY_LOG_DIR} public/bundles public/compiled public/uploads"
for directory in ${directories}; do
    mkdir -p ${directory}
done

php bin/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}
php bin/console doctrine:migrations:execute  --env=${SYMFONY_ENVIRONMENT} -v --force
php bin/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}

for directory in ${directories}; do
    chown -R apache:apache ${directory}
done

exec "$@"
