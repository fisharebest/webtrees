Zend Framework 1 for Composer
=============================

This is a mirror of the official Zend Framework 1 subversion repository, with some modifications for improved [Composer](http://getcomposer.org/) integration. This package can also be found at [Packagist](http://packagist.org/packages/bombayworks/zendframework1).

## Why?

There are several reasons for using this package over of the official package:

* The following folders have been removed: `demos, documentation, externals, extras/documentation, extras/tests, src, tests`. This reduces the number of files from over 72000 to below 3500. It also reduces size from ~532MB to ~33MB.
* Improved autoloading. Explicit `require_once` calls in the source code has been commented out to rely on composer autoloading, this reduces the number of included files to a minimum.
* Composer integrates better with github than it does with subversion.

## How to use

Add `"bombayworks/zendframework1": "1.*"` to the require section of your composer.json, include the composer autoloader and you're good to go.

## Automatic mirroring

You dont need to worry about future releases missing from this repository. A cron job has been setup to automatically commit, tag and push new releases to this repository.
