[![Build Status](https://travis-ci.org/fisharebest/php-polyfill.svg?branch=master)](https://travis-ci.org/fisharebest/php-polyfill)
[![Coverage Status](https://coveralls.io/repos/fisharebest/php-polyfill/badge.svg?branch=master&service=github)](https://coveralls.io/github/fisharebest/php-polyfill?branch=master)

PHP Polyfill
============

This project combines a number of other PHP polyfill libraries as well as
providing many polyfills of its own.

Where possible, our polyfills are passed upstream to other packages.
Ideally, there would be no code in this package - just a list of
dependencies that work together.

The symfony project will only accept contributions that are 100% compatible
with the native PHP implementation.  In many cases, this isn't actually
possible - it’s often why the functions were added to PHP in the first place!

So, this project provides implementations that are often “good enough” to get
your application working on old servers.  Restrictions and limitations are
described below.

Usage
=====

Add the dependency to your `composer.json` and allow autoloading magic to do the rest.

```json
{
    "require": {
        "fisharebest/php-polyfill": "~1.0",
    },
}
```

The following polyfill libraries will be loaded automatically:

 - `symfony/polyfill` - the core polyfills
   - `symfony/intl` - polyfills for intl library functions
   - `ircmaxell/password_compat` - polyfills for password functions
   - `paragonie/random_compat` - polfills for random number functions
 - `fisharebest/ext-calendar` - polyfills for the calendar library functions
 - `jakeasmith/http_build_url` - polyfill for the function `http_build_url()`
 
The following polyfills are provided by this package:

PHP 5.3
=======

 - If the server has enabled “magic quotes”, these are removed.
 - If the server has enabled "bug_compat_42", this is disabled.


PHP 5.4
=======

 - `http_response_code()` - The native function allows you to get the current
status code, even if it was set using another function, such as `header()`.
This implementation can only get the current status code if it was also set by
`http_response_code()`.

PHP 5.5.9 (Ubuntu 14.04 32 bit)
===============================

- `gzopen()` - Wrongly implemented as `gzopen64()`.
- `gzseek()` - Wrongly implemented as `gzseek64()`.
- `gztell()` - Wrongly implemented as `gztell64()`.

Contributions
=============

Please follow the existing code style and write unit-tests where you can.

License
=======

This package is dual licensed under both the [MIT](LICENSE-MIT.md) and
[GPLv3](LICENSE-GPLv3.md) licenses.  Use whichever makes you happiest.
