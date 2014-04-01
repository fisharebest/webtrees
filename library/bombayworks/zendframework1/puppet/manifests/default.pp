group { "puppet":
    ensure => "present",
}

# install vim and all packages required to build PHP
$packages = [ "vim", "curl", "libxpm-dev", "libmcrypt-dev", "libbz2-dev", "libcurl4-gnutls-dev", "libjpeg62-dev", "libpng12-dev", "libfreetype6-dev", "libt1-dev", "libgmp3-dev", "libmysqlclient-dev", "libpq-dev", "libpcre3-dev", "libxml2-dev", "libxslt-dev", "make"]

package { $packages :
    ensure => installed,
}

# Update .bashrc
$serial = "2012043001"
$serialfile = "/var/log/pe-bashrc-update.serial"
exec { "install-bashrc-update":
    command => "/bin/cat /vagrant/puppet/scripts/pe.sh >> /home/vagrant/.bashrc \
                && /bin/echo \"$serial\" > \"$serialfile\"",
    unless  => "/usr/bin/test \"`/bin/cat $serialfile 2> /dev/null`\" = \"$serial\"",
}

