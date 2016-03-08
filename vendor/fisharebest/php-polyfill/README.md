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
your application working on both new/old versions of PHP at the same time.
Restrictions and limitations are described below.

Of course, if you know exactly which PHP functions in your project might not
be available on other servers, then you can just include the polyfills you need.
But if life is too short, then just include `fisharebest/php-polyfill` and not
worry about it.

Usage
=====

Add the dependency to your `composer.json` and allow autoloading magic to do the rest.

```json
{
    "require": {
        "fisharebest/php-polyfill": "~1.4",
    },
}
```

The following polyfill libraries will be loaded automatically:

 - `fisharebest/ext-calendar` - polyfills for the calendar functions
 - `jakeasmith/http_build_url` - polyfill for the function `http_build_url()`
 - `symfony/polyfill-apcu` - APCu functions
 - `symfony/polyfill-iconv` - iconv functions
 - `symfony/polyfill-intl-grapheme` - grapheme_* functions
 - `symfony/polyfill-intl-icu` - intl functions and classes
 - `symfony/polyfill-intl-normalizer` - normalizer functions and classes
 - `symfony/polyfill-intl-mbstring` - mbstring functions
 - `symfony/polyfill-intl-util` - core polyfill functions
 - `symfony/polyfill-intl-xml` - utf8_encode/decode functions
 - `symfony/polyfill-php54` - PHP 5.4 functions
 - `symfony/polyfill-php55` - PHP 5.5 functions
   - `ircmaxell/password_compat` - polyfills for password functions
 - `symfony/polyfill-php56` - PHP 5.6 functions
 - `symfony/polyfill-php70` - PHP 7.0 functions
    - `paragonie/random_compat` - random number functions
 
The following additional polyfills are provided by this package:

PHP 5.3
=======

 - If the server has enabled `magic quotes`, these are removed.
 - If the server has enabled `bug_compat_42`, this is disabled.


PHP 5.4
=======

 - `http_response_code()` - The native function allows you to get the current
status code, even if it was set using another function, such as `header()`.
This implementation can only get the current status code if it was also set by
`http_response_code()`.

PHP (general)
=============

 - Some builds of PHP (such as the one used by strato.de) do not define the constant `INF`.

Contributions
=============

Please follow the existing code style and write unit-tests where you can.

License
=======

This package is dual licensed under both the [MIT](LICENSE-MIT.md) and
[GPLv3](LICENSE-GPLv3.md) licenses.  Use whichever makes you happiest.
