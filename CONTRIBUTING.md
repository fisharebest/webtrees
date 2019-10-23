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
* Help us maintaining our github page from the repository [webtrees.github.io](https://github.com/webtrees/webtrees.github.io).

## How to setup a development environment

You will need three tools, in addition to the requirements of a live installation.

* `git` - to fetch the latest development code, merge changes, create pull requests, etc.
* [`composer`](https://getcomposer.org) - to install PHP dependencies, build releases, and run tests.
* [`npm`](https://nodejs.org/en/download/package-manager) - to install CSS/JS dependencies and create portable/minified `.css` / `.js` files.

You do not need to understand the details of `composer` and `npm`.  You just need to be able to type a few commands at a console.

If you are going to submit your work to the project, you will need a basic understanding of `git`.  The workflow for using git is covered below in the section on "Pull Requests".

### GIT

The project has been running for many years, and has a lot of history.
This means that the full repository is over 600MB.

If you are only interested in the latest version of the code, you can use a
"shallow clone", which is about 30MB.

Use `git clone --depth 1 https://github.com/fisharebest/webtrees`.

### Composer

The instructions below assume you have created an alias/shortcut for `composer`.
If not, you'll need to replace `composer` with `php /path/to/your/copy/of/composer.phar`.

* You would usually run `composer install` before starting any develoment.  This loads all the development tools, including the PHP debug bar, the build scripts, the analysis and testing tools.  You would then run `composer install --no-dev` before committing any changes.

* The PHP Debug Bar can set a large number of HTTP headers, and you may need to increase the size of buffers in your webserver configuration.  There are some notes in the file `app/Http/Middleware/UseDebugbar.php`.

* You can use a "pre-commit hook" to run checks on your code before you commit them to your local repository.  To do this, rename the file `.git/hooks/pre-commit.sample` to `.git/hooks/pre-commit` and then add this line at the end of the file: `composer webtrees:pre-commit-hook`. 

### NPM

* After modifying any CSS or JS files, you'll need to rebuild the files in `public/js` and `public/css`.  You do this with the command `npm run production`.

## Third-party libraries and compiled files in the source tree

For historic reasons, we include certain third-party libraries and compiled
files in our source tree.  This is usually considered to be bad practice.
We do it because we have a large number of testers and users who have become
accustomed to downloading the latest source code and running it without any build step.

We include the non-development PHP libraries from `/vendor`.
These are created using the command `composer install --no-dev`.

We include the compiled JS and CSS assets from `/public/{css,js}`).
These are created using the command `npm run production`.


## Creating a pull request

[TODO]



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

