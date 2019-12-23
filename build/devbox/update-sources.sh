#!/bin/bash

rsync -urzvhl /vagrant/ /home/vagrant/zcorrecteurs \
    --exclude=.git \
    --exclude=.gitignore \
    --exclude=vendor \
    --exclude=web/bundles \
    --exclude=web/compiled \
    --exclude=app/cache \
    --exclude=app/logs \
    --exclude=composer.phar \
    --exclude=.vagrant \
    --exclude=Vagrantfile \
    --exclude=.idea \
    --delete \
    --delete-excluded
