# How to contribute

Thanks for taking the time to contribute!

The following is a set of guidelines for contributing to Webtrees. These are mostly guidelines, not rules. 
Use your best judgment, and feel free to propose changes to this document in a pull request.

## How to submit a patch or feature

If you want to submit a patch or feature, please create a pull request on Github. There are different branches for different versions of webtrees:

* For webtrees 2.0, use the `master` branch
* For the latest webtrees 1.7.*, use the `1.7` branch

Before submitting a pull request make sure you have [tested the code](#how-to-test) 
and [followed the coding conventions](#coding-conventions).

Please read more about [setting up your environment](#how-to-setup-the-environment) for development.

## How to start

You can start contributing by

* submitting patches or features
* writing tests to [increase the test coverage](https://coveralls.io/github/fisharebest/webtrees?branch=master)
* creating or updating [translations](#translations)
* Join our slack. The invitation link will be updated on this line.

## How to setup the environment

You need

* PHP >=7.1.8 (for webtrees 2.0; or PHP >=5.3.9 for webtrees 1.7)
* MySQL 5
* git
* composer

1. Fork and clone the repository
2. Run `composer install` to update the PHP dependencies.
3. Run `npm install` to update the JS and HTML files.
4. Run `php -S localhost:8080` and open your browser at [localhost:8080](http://localhost:8080)
5. Go through the install process

## How to test

Install PHPUnit by running `composer install`

Then run the tests with `vendor/bin/phpunit`

## Coding conventions

Your code should follow the [PSR-12](https://www.php-fig.org/psr/psr-12/) Extended coding style guide.

Please do not contribute your IDE directories (e.g. `.idea` or `.vscode`).

## Translations

Please refer to the guide from the [official wiki](https://wiki.webtrees.net/en/Category:Translation_Guidelines).

There is a [translators forum](http://webtrees.net/index.php/en/forum/8-translation) where you can discuss any issues relating to translation.

Updates to translations should be made at [translate.webtrees.net](https://translate.webtrees.net). 
Changes made there will be pushed to webtrees git repository periodically and will be available 
on the development version of webtrees. They will be included in the next release of webtrees.

