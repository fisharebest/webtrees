# middlewares/client-ip

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to detect the client ip and save it as a request attribute.

## Requirements

* PHP >= 7.0
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http message implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/client-ip](https://packagist.org/packages/middlewares/client-ip).

```sh
composer require middlewares/client-ip
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\ClientIp(),

    function ($request) {
        //Get the client ip
        $ip = $request->getAttribute('client-ip');
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `proxy(array $ips = [], array $headers = ['Forwarded', 'Forwarded-For', 'Client-Ip', 'X-Forwarded', 'X-Forwarded-For', 'X-Cluster-Client-Ip'])`

Configure the detection through proxies. The first argument is an array of ips or cidr of the trusted proxies. If it's empty, no ip filtering is made. The second argument is a list of the headers to inspect. If it's not defined, uses the default value `['Forwarded', 'Forwarded-For', 'Client-Ip', 'X-Forwarded', 'X-Forwarded-For', 'X-Cluster-Client-Ip']`. Disabled by default.

```php
//Use proxies
$middleware = (new Middlewares\ClientIp())->proxy();

//Trust only some proxies by ip
$middleware = (new Middlewares\ClientIp())->proxy(['10.10.10.10', '10.10.10.11']);

//Trust only some proxies by ip using a specific header
$middleware = (new Middlewares\ClientIp())->proxy(['10.10.10.10', '10.10.10.11'], ['X-Forwarded-For']);

// Trust only some proxies by cidr range
// usefull when you have an autoscaled proxy(like haproxy) in a subnet
$middleware = (new Middlewares\ClientIp())->proxy(['192.168.0.0/16', '10.0.0.0/8']);

```

#### `remote($remote = true)`

Used to get the ip from localhost environment using [http://ipecho.net/plain](http://ipecho.net/plain). Disabled by default.

#### `attribute(string $attribute)`

The attribute name used to store the ip in the server request. By default is `client-ip`.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/client-ip.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/client-ip/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/client-ip.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/client-ip.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/a9cfb07f-bb83-477a-bca8-582709c92fec.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/client-ip
[link-travis]: https://travis-ci.org/middlewares/client-ip
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/client-ip
[link-downloads]: https://packagist.org/packages/middlewares/client-ip
[link-sensiolabs]: https://insight.sensiolabs.com/projects/a9cfb07f-bb83-477a-bca8-582709c92fec
