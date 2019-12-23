# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.require_version ">= 2.2.0"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # Ubuntu box published by Hashicorp.
  # https://app.vagrantup.com/hashicorp/boxes/bionic64
  config.vm.box = "hashicorp/bionic64"
  config.vm.box_version = "1.0.282"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # NOTE: This will enable public access to the opened port
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Mount the local sources inside the VM.
  config.vm.synced_folder ".", "/vagrant"

  # Configure Virtualbox provider.
  # https://www.vagrantup.com/docs/virtualbox/configuration.html
  config.vm.provider "virtualbox" do |vb|
  #   vb.memory = "1024"
  end

  config.vm.provision "shell", path: "build/devbox/provision.sh"
  SHELL
end
