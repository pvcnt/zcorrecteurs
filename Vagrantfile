# -*- mode: ruby -*-
# vi: set ft=ruby :

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

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.require_version ">= 2.2.0"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # Ubuntu box published by Hashicorp.
  # https://app.vagrantup.com/hashicorp/boxes/bionic64
  config.vm.box = "hashicorp/bionic64"
  config.vm.box_version = "1.0.282"

  config.vm.network :private_network, ip: "192.168.33.10"

  # Mount the local sources inside the VM.
  config.vm.synced_folder ".", "/vagrant"

  # Configure Virtualbox provider.
  # https://www.vagrantup.com/docs/virtualbox/configuration.html
  config.vm.provider "virtualbox" do |vb|
  #   vb.memory = "1024"
  end

  config.vm.provision "shell", path: "build/devbox/provision.sh"
end
