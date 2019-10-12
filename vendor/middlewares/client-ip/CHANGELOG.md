# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.3.0] - 2019-03-28
### Added
- Strip square brackets from server provided IPs, workaround for [PHP Bug #76919](https://bugs.php.net/bug.php?id=76919)

## [1.2.0] - 2019-01-13
### Added
- Proxy option supports cidr range values in addition to ips [#13], [#14]

### Fixed
- Use `phpstan` as a dev dependency to detect bugs

## [1.1.0] - 2018-08-04
### Added
- PSR-17 support

## [1.0.2] - 2018-06-28
### Fixed
- Prevent spoofing attacks [#11]

## [1.0.1] - 2018-04-28
### Fixed
- Support for `Forwarded` and `X-Forwarded` headers, that use a different syntax [#9]
- Updated testing libraries

## [1.0.0] - 2018-01-27
### Added
- Improved testing and added code coverage reporting
- Added tests for PHP 7.2

### Changed
- Upgraded to the final version of PSR-15 `psr/http-server-middleware`

### Fixed
- Updated license year

## [0.7.0] - 2017-11-13
### Changed
- Replaced `http-interop/http-middleware` with  `http-interop/http-server-middleware`.

### Removed
- Removed support for PHP 5.x.

## [0.6.0] - 2017-09-21
### Changed
- Append `.dist` suffix to phpcs.xml and phpunit.xml files
- Changed the configuration of phpcs and php_cs
- Upgraded phpunit to the latest version and improved its config file
- Updated to `http-interop/http-middleware 0.5`

## [0.5.0] - 2017-07-13
### Fixed
- Resolved bug that would fail to set any IP when proxy headers are enabled and no header matched

## [0.4.0] - 2017-04-13
### Changed
- Removed the `headers()` option and replaced by `proxy()` that accept two arguments: the ip of the trust proxies and the headers used
- The proxy headers have priority over `REMOTE_ADDR` variable, but they are disabled by default

## [0.3.0] - 2016-12-26
### Changed
- Updated tests
- Updated to `http-interop/http-middleware 0.4`
- Updated `friendsofphp/php-cs-fixer 2.0`

## [0.2.0] - 2016-11-27
### Changed
- Updated to `http-interop/http-middleware 0.3`

## 0.1.0 - 2016-10-10
First version

[#9]: https://github.com/middlewares/client-ip/issues/9
[#11]: https://github.com/middlewares/client-ip/issues/11
[#13]: https://github.com/middlewares/client-ip/issues/13
[#14]: https://github.com/middlewares/client-ip/issues/14

[1.3.0]: https://github.com/middlewares/client-ip/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/middlewares/client-ip/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/middlewares/client-ip/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/middlewares/client-ip/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/middlewares/client-ip/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/middlewares/client-ip/compare/v0.7.0...v1.0.0
[0.7.0]: https://github.com/middlewares/client-ip/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/middlewares/client-ip/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/middlewares/client-ip/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/middlewares/client-ip/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/middlewares/client-ip/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/middlewares/client-ip/compare/v0.1.0...v0.2.0
