#!/bin/bash
# zCorrecteurs.fr is the software behind www.zcorrecteurs.fr
#
# Copyright (C) 2012-2020 Corrigraphie
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

set -o nounset
set -e

function build_webserver {
    # Build Docker container.
    docker build -f build/webserver/Dockerfile -t webserver .

    # Run Doctrine migrations.
    docker run --rm --net=host webserver php app/console doctrine:migrations:execute --force

    # Restart container.
    docker rm -f webserver
    docker run --rm --net=host -d --name webserver -e SYMFONY_ENVIRONMENT=dev -e SYMFONY_DEBUG=true webserver serve
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