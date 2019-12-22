#!/bin/bash

set -e

if [ -z "$DATABASE_HOST" ]; then
    echo "You must define DATABASE_HOST"
    exit 1
fi

if [ -z "$DATABASE_USER" ]; then
    echo "You must define $DATABASE_USER"
    exit 1
fi

if [ -z "$MYSQL_PASSWORD" ]; then
    echo "You must define MYSQL_PASSWORD"
    exit 1
fi

if [ -z "$MYSQL_DATABASE" ]; then
    echo "You must define MYSQL_DATABASE"
    exit 1
fi

envsubst '\$MYSQL_HOST \$MYSQL_USER \$MYSQL_PASSWORD \$MYSQL_DATABASE' \
  < /etc/sphinxsearch/sphinx.conf.template \
  > /etc/sphinxsearch/sphinx.conf

/usr/local/bin/reindex.sh

exec "$@"