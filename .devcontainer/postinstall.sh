sudo cp .devcontainer/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
composer update 
composer install 
sudo chmod a+x $(pwd)
sudo rm -rf /var/www/html
sudo ln -s $(pwd) /var/www/html
