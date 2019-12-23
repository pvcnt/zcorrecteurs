#!/bin/bash

set -o nounset
set -e

function build_webserver {
    docker build -f build/webserver/Dockerfile -t webserver .
}

function build_sphinx {
    docker build -f build/sphinx/Dockerfile -t sphinx .
}

function build_all {
  build_sphinx
  build_webserver
}

if [ "$#" -eq 0 ]; then
  echo 'Must specify at least one component to build'
  exit 1
fi

cd /home/vagrant/zcorrecteurs
update-sources
for component in "$@"; do
  build_$component
done