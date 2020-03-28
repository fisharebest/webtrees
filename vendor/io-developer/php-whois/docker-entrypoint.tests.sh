#!/usr/bin/env sh

composer install

php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests $@
