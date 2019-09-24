# Flysystem Chroot Adapter

[![Author](http://img.shields.io/badge/author-@fisharebest-blue.svg?style=flat-square)](https://github.com/fisharebest)
[![Latest Stable Version](https://poser.pugx.org/fisharebest/flysystem-chroot-adapter/v/stable.svg)](https://packagist.org/packages/fisharebest/flysystem-chroot-adapter)
[![Build Status](https://travis-ci.org/fisharebest/flysystem-chroot-adapter.svg?branch=master)](https://travis-ci.org/fisharebest/flysystem-chroot-adapter)
[![Coverage Status](https://coveralls.io/repos/github/fisharebest/flysystem-chroot-adapter/badge.svg?branch=master)](https://coveralls.io/github/fisharebest/flysystem-chroot-adapter?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/flysystem-chroot-adapter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/flysystem-chroot-adapter/?branch=master)
[![StyleCI](https://github.styleci.io/repos/166235152/shield?branch=master)](https://github.styleci.io/repos/166235152)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This adapter creates a new filesystem from a sub-folder of an existing filesystem.

## Installation

```bash
composer require fisharebest/flysystem-chroot-adapter
```

## Usage

```php
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Fisharebest\Flysystem\Adapter\ChrootAdapter

// Write a file to a filesystem.
$filesystem = new Filesystem(new Local(__DIR__));
$filesystem->write('foo/bar/fab/file.txt', 'hello world!');

// Create a chroot filesystem from the foo/bar folder.
$chroot = new Filesystem(new ChrootAdapter($filesystem, 'foo/bar'));

// And read it back from the chroot.
$chroot->read('fab/file.txt'); // 'hello world!'
```
