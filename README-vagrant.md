# Vagrant development box for [fisharebest/webtrees](https://github.com/fisharebest/webtrees)

Don't use this repo directly. It is meant to work as sub-module in fisharebest/webtrees under `tools/vagrant`. Please checkout fisharebest/webtrees and ran there the VAGRANT.sh.

# Vagrant - Usage

This uses [Vagrant](http://vagrantup.com) and [VirtualBox](http://virtualbox.org) to set up a virtual machine with an installed webserver (either apache2.4 or nginx with PHP5.4).
* Install first VirtualBox and Vagrant on your host.

## Linux

### Get the VM running

* We are assuming you are in the project root directory. All here mentioned paths are starting from there.

#### Permissions on your host
* you need to set the owner group of your project root and sub directories to www-data(gid: 33) by
 * run `sudo chgrp -Rc 33 .` (make sure you are in the project root before running!)

#### Run the vagrant box
* run `./VAGRANT.sh`
 * it copies `tools/vagrant/linux/apache2` to project root, so you can control vagrant via PhpStorm Tools menu
* run `vagrant up`
 * Attention: you need a good internet connection first time you run `vagrant up`. It downloads and install a lot of packages into the vagrant box

* run `sudo tools/vagrant/linux/configure-vagrant-host.sh` to set up needed hosts-ip resolution automatically in `/etc/hosts` or
 * edit `/etc/hosts` by hand add following entries
  - `192.168.56.181    webtrees.nginx.dev www.webtrees.nginx.dev`
  - `192.168.56.182    webtrees.apache2.dev www.webtrees.apache2.dev`

* You can now login via ssh running `vagrant ssh`
* The website is reachable via
 * apache2 based: `webtrees.apache2.dev`
 * nginx based  : `webtrees.nginx.dev`
 
### Set up webtrees

* call http://webtrees.apache2.dev/setup.php in your browser
* On page Connection to database server
 * Database user account: `webtrees_dbu`
 * Database password: `webtrees`
* Next page 'Database and table names'
 * Database name: `webtrees_dev`
 * Table prefix: `webtrees_`
* Next page 'System settings' (you can choose here everything on your own)
 * Your name: `Administrator`
 * Login ID: `admin`
 * Password: `webtrees`
 * Email address: `admin@webtrees.apache2.dev`
* Now you can login with
 * Username: `admin`
 * Password: `webtreeswebtrees`
 
### Trouble shooting

* During `vagrant up` or vagrant provision occurs the message _Warning: Could not retrieve fact fqdn_. Just ignore it, nothing is wrong.
* If `vagrant up` doesn't run successfully through
 * It could be your hosts file permissions on project root prevent setting up the share folder, execute `sudo chgrp -Rc 33 .` in project root
 * execute `vagrant provision` after every change

## Windows

... to be done ...


# Updating

* go to https://puphpet.com/
* upload either the `tools/vagrant/linux/apache2/puphpet/config.yaml`, `tools/vagrant/linux/nginx/puphpet/config.yaml`
* change your settings
* download the new configuration
* extract the zip contents under `tools/vagrant/linux/` or `tools/vagrant/windows/` (skip the cryptic named folder, just the content in it)

