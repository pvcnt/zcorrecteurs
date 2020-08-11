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

set -e

# Install required dependencies.
apt-get update -y
#apt-get upgrade -y
apt-get install -y \
  apt-transport-https \
  ca-certificates \
  curl \
  gnupg-agent \
  software-properties-common \
  git \
  curl \
  mysql-server-5.7

# Install Docker.
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add -
add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
apt-get update -y
apt-get install -y docker-ce docker-ce-cli containerd.io
usermod -aG docker vagrant
systemctl enable docker

# Remove apt cache to slim down the VM size.
apt-get autoremove -y --purge
apt-get clean

# Install build scripts.
ln -f -s /vagrant/build/devbox/update-sources.sh /usr/local/bin/update-sources
chmod +x /usr/local/bin/update-sources

ln -f -s /vagrant/build/devbox/build.sh /usr/local/bin/build
chmod +x /usr/local/bin/build

# Create a MySQL database and an associated user.
echo "create user 'zcodev'@'%' identified by 'pass';" | mysql -f
echo "create database zcodev;" | mysql -f
echo "grant all privileges on zcodev.* to 'zcodev'@'%'; flush privileges;" | mysql -f

# Checkout sources a first time.
update-sources > /dev/null
chown -R vagrant:vagrant /home/vagrant
