#!/bin/bash

# Install required dependencies.
sudo apt-get update -y
#sudo apt-get upgrade -y
sudo apt-get install -y \
  apt-transport-https \
  ca-certificates \
  curl \
  gnupg-agent \
  software-properties-common \
  git \
  curl \
  mysql-server-5.7

# Install Docker.
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo apt-get update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io
sudo usermod -aG docker $USER
sudo systemctl enable docker

# Remove apt cache to slim down the VM size.
sudo apt-get autoremove -y --purge
sudo apt-get clean

# Install build scripts.
ln -s /vagrant/build/devbox/update-sources.sh /usr/local/bin/update-sources
chmod +x /usr/local/bin/update-sources

ln -s /vagrant/build/devbox/build.sh /usr/local/bin/build
chmod +x /usr/local/bin/build

# Update sources a first time.
update-sources > /dev/null
chown -R vagrant:vagrant /home/vagrant

# Create a MySQL database and an associated user.
echo "create user 'zcodev'@'localhost' identified by 'pass';" > sudo mysql
echo "create database zcodev;" | sudo mysql
echo "grant all privileges on * . * to 'zcodev'@'localhost'; flush privileges;" | sudo mysql