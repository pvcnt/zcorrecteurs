#!/bin/bash

set -e

if [ -z "$DATABASE_HOST" ]; then
    echo "You must define DATABASE_HOST"
    exit 1
fi
if [ -z "$DATABASE_USER" ]; then
    echo "You must define DATABASE_USER"
    exit 1
fi
if [ -z "$DATABASE_PASSWORD" ]; then
    echo "You must define DATABASE_PASSWORD"
    exit 1
fi
if [ -z "$DATABASE_BASE" ]; then
    echo "You must define DATABASE_BASE"
    exit 1
fi

envsubst '\$DATABASE_HOST \$DATABASE_USER \$DATABASE_PASSWORD \$DATABASE_BASE' \
  < /etc/sphinxsearch/sphinx.conf.template \
  > /etc/sphinxsearch/sphinx.conf

function reindex {
  /usr/local/bin/reindex
}

function serve {
  reindex
  searchd --nodetach
}

case "$1" in
  serve) serve;;
  reindex) reindex;;
  *) exec "$@";;
esac