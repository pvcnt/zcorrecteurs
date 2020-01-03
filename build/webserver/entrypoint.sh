#!/bin/bash
# zCorrecteurs.fr is the software behind www.zcorrecteurs.fr
#
# Copyright (C) 2012-2019 Corrigraphie
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

set -e

function ensure_dirs {
  # Some directories do need to exist and have the correct permissions.
  directories="${SYMFONY_CACHE_DIR} ${SYMFONY_LOG_DIR} web/bundles web/compiled"
  for directory in ${directories}; do
      mkdir -p ${directory}
      chown -R apache:apache ${directory}
  done
}

function serve {
  ensure_dirs
  php app/console assets:install web -v --env=${SYMFONY_ENVIRONMENT}
  php app/console cache:clear -v  --env=${SYMFONY_ENVIRONMENT}
  ensure_dirs
  httpd -D FOREGROUND
}

function console {
  ensure_dirs
  php app/console "$@"
  ensure_dirs
}

# Define some commands for convenience.
case "$1" in
  serve) serve;;
  console) console "${@:2}";;
  *) ensure_dirs
     exec "$@";;
esac