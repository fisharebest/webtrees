# Aura.Router

Powerful, flexible web routing for PSR-7 requests.

## Installation and Autoloading

This package is installable and PSR-4 autoloadable via Composer as
[aura/router][].

Alternatively, [download a release][], or clone this repository, then map the
`Aura\Router\` namespace to the package `src/` directory.

## Dependencies

This package requires PHP 5.5 or later; it has been tested on PHP 5.6, PHP 7,
PHP 7.1 and HHVM. We recommend using the latest available version of PHP as a
matter of principle.

Aura library packages may sometimes depend on external interfaces, but never on
external implementations. This allows compliance with community standards
without compromising flexibility. For specifics, please examine the package
[composer.json][] file.

## Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/auraphp/Aura.Router/badges/quality-score.png?b=3.x)](https://scrutinizer-ci.com/g/auraphp/Aura.Router/)
[![Code Coverage](https://scrutinizer-ci.com/g/auraphp/Aura.Router/badges/coverage.png?b=3.x)](https://scrutinizer-ci.com/g/auraphp/Aura.Router/)
[![Build Status](https://travis-ci.org/auraphp/Aura.Router.png?branch=3.x)](https://travis-ci.org/auraphp/Aura.Router)

To run the unit tests at the command line, issue `composer install` and then
`./vendor/bin/phpunit` at the package root. (This requires [Composer][] to be
available as `composer`.)

This package attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

## Community

To ask questions, provide feedback, or otherwise communicate with other Aura
users, please join our [Google Group][], follow [@auraphp][], or chat with us
on Freenode in the #auraphp channel.

## Documentation

This package is fully documented [here](./docs/index.md).

There is also a video demonstration on [YouTube](https://www.youtube.com/watch?v=DA60mYS-YoM):

<p align="center">
    <a href="https://www.youtube.com/watch?v=DA60mYS-YoM">
        <img src="https://img.youtube.com/vi/DA60mYS-YoM/0.jpg" alt="Version 3 video tutorial on Aura.Router" />
    </a>
</p>

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/
[Google Group]: http://groups.google.com/group/auraphp
[@auraphp]: http://twitter.com/auraphp
[download a release]: https://github.com/auraphp/Aura.Router/releases
[aura/router]: https://packagist.org/packages/aura/router
[composer.json]: ./composer.json
