# -*- mode: ruby -*-
# vi: set ft=ruby :


# Inline provisioning shell script
@script = <<SCRIPT

echo -e '\nexport PATH=~/.composer/vendor/bin:$PATH\n' >> ~/.bashrc

# Switch to PHP7
newphp 7

# rebuild PHP7
#makephp 7

echo ""
echo "** SSH into the box to run the tests. use newphp to switch between versions and makephp 7 to rebuld PHP  7**"
echo "** Use 'newphp nn' to switch between versions (e.g. newphp 54)**"
echo "** Install PHPUnit via composer global require phpunit/phpunit:~n.n for the version required**"
echo ""
SCRIPT


# Vagrant configuration
VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |c|
  c.vm.define "zf1dev", primary: true do |config|
    config.vm.box = 'rasmus/php7dev'
    # config.vm.network :forwarded_port, guest: 80, host: 8889
    config.vm.hostname = "zf1dev.localhost"
    
    config.vm.provision 'shell', inline: @script

    config.vm.provider "virtualbox" do |vb|
      vb.customize ["modifyvm", :id, "--memory", "1024"]
    end

  end
end


    # config.vm.customize [
    #   # 'modifyvm', :id, '--chipset', 'ich9', # solves kernel panic issue on some host machines
    #   # '--uartmode1', 'file', 'C:\\base6-console.log' # uncomment to change log location on Windows
    #   "setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"
    # ]
