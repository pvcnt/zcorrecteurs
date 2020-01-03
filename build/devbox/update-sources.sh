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

rsync -urzvhl /vagrant/ /home/vagrant/zcorrecteurs \
    --exclude=.git \
    --exclude=.gitignore \
    --exclude=/vendor \
    --exclude=/web/bundles \
    --exclude=/web/compiled \
    --exclude=/app/cache \
    --exclude=/app/logs \
    --exclude=composer.phar \
    --exclude=.vagrant \
    --exclude=Vagrantfile \
    --exclude=.idea \
    --delete \
    --delete-excluded
