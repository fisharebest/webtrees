# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.0] - 2018-01-24

### Changed

- Updated to the final version of PSR-15 (`psr/http-server-middleware`)

## [0.7.1] - 2018-01-12

### Added

- Added more tests for 100% code coverage
- Added php7.2 to travis

### Changed

- Some internal code cleanup

## [0.7.0] - 2017-12-14

### Added

- Added `__invoke()` magic method to use the dispatcher as a callable

## [0.6.0] - 2017-11-12

### Changed

- Switch from `http-interop/http-middleware` to `http-interop/http-server-middleware`
- Simplified internal code

## [0.5.0] - 2017-09-22

### Changed

- Changed `MatcherInterface` to allow use callables.
- Updated to `http-interop/http-middleware#0.5`
- Updated phpunit to `^6.0`

## [0.4.0] - 2017-03-01

### Changed

- Replaced container-interop by `psr/container`

## [0.3.0] - 2017-01-28

### Added

- New `Middleland\Matchers\Pattern` to filter by path patterns
- New `Middleland\Matchers\Accept` to filter by Accept headers

### Fixed

- `Middleland\Matchers\Path` compare the directory name, instead the path.

## [0.2.0] - 2017-01-09

### Added

- Support for [container-interop](https://github.com/container-interop/container-interop), to create middleware components on demand

## 0.1.0 - 2017-01-09

First version

[1.0.0]: https://github.com/oscarotero/middleland/compare/v0.7.1...v1.0.0
[0.7.1]: https://github.com/oscarotero/middleland/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/oscarotero/middleland/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/oscarotero/middleland/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/oscarotero/middleland/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/oscarotero/middleland/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/oscarotero/middleland/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/oscarotero/middleland/compare/v0.1.0...v0.2.0
