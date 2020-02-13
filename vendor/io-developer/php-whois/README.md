# PHP WHOIS

[![Build Status](https://travis-ci.org/io-developer/php-whois.svg?branch=master)](https://travis-ci.org/io-developer/php-whois)
[![PHP version](https://img.shields.io/badge/php-%3E%3D5.4-8892BF.svg)](https://secure.php.net/)
[![Packagist](https://img.shields.io/packagist/v/io-developer/php-whois.svg)](https://packagist.org/packages/io-developer/php-whois)

PHP WHOIS client implementation. Sends the queries directly to the WHOIS services.

## Use case
 * Raw and parsed domain lookup
 * Raw and parsed ASN routes lookup
 * Direct queries to TLD/ASN hosts
 * Extending and customizing the default hosts, parsers, etc.
 * Proxying via CurlLoader

## Installation

##### System requirements:
* PHP >= __5.4__ (compatible with __7.*__ up to __7.3__)
* php-curl
* php-intl
* php-mbstring
* php-memcached + Memcached server (both optional)
* Allowed port 43 in firewall

##### Project requirements:
* PSR-4 autoloader

##### Composer:
````
composer require io-developer/php-whois
````
or composer.json:
````
"require": {
    "io-developer/php-whois": "*"
}
````


## Usage

### Domain lookup

##### How to get summary about domain:
```php
<?php

use Iodev\Whois\Whois;

// Creating default configured client
$whois = Whois::create();

// Checking availability
if ($whois->isDomainAvailable("google.com")) {
    print "Bingo! Domain is available! :)";
}

// Supports Unicode (converts to punycode)
if ($whois->isDomainAvailable("почта.рф")) {
    print "Bingo! Domain is available! :)";
}

// Getting raw-text lookup
$response = $whois->lookupDomain("google.com");
print $response->getText();

// Getting parsed domain info
$info = $whois->loadDomainInfo("google.com");
print_r([
    'Domain created' => date("Y-m-d", $info->getCreationDate()),
    'Domain expires' => date("Y-m-d", $info->getExpirationDate()),
    'Domain owner' => $info->getOwner(),
]);

```

##### Exceptions on domain lookup:
```php
<?php

use Iodev\Whois\Whois;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

try {
    $whois = Whois::create();
    $info = $whois->loadDomainInfo("google.com");
    if (!$info) {
        print "Null if domain available";
        exit;
    }
    print $info->getDomainName() . " expires at: " . date("d.m.Y H:i:s", $info->getExpirationDate());
} catch (ConnectionException $e) {
    print "Disconnect or connection timeout";
} catch (ServerMismatchException $e) {
    print "TLD server (.com for google.com) not found in current server hosts";
} catch (WhoisException $e) {
    print "Whois server responded with error '{$e->getMessage()}'";
}
```

##### Proxy with SOCKS5:
```php
<?php

use Iodev\Whois\Loaders\CurlLoader;
use Iodev\Whois\Whois;

$loader = new CurlLoader();
$loader->replaceOptions([
    CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
    CURLOPT_PROXY => "127.0.0.1:1080",
    //CURLOPT_PROXYUSERPWD => "user:pass",
]);
$whois = Whois::create($loader);

var_dump([
    'ya.ru' => $whois->loadDomainInfo('ya.ru'),
    'google.de' => $whois->loadDomainInfo('google.de'),
]);
```

##### TLD hosts customization:
```php
<?php

use Iodev\Whois\Whois;
use Iodev\Whois\Modules\Tld\TldServer;
use Iodev\Whois\Modules\Tld\TldParser;

$whois = Whois::create();

// Define custom whois host
$customServer = new TldServer(".custom", "whois.nic.custom", false, TldParser::create());

// Or define the same via assoc way
$customServer = TldServer::fromData([
    "zone" => ".custom",
    "host" => "whois.nic.custom",
]);

// Add custom server to existing whois instance
$whois->getTldModule()->addServers([$customServer]);

// Now it can be utilized
$info = $whois->loadDomainInfo("google.custom");

var_dump($info);
```

##### TLD default/fallback servers:
```php
<?php

use Iodev\Whois\Whois;
use Iodev\Whois\Modules\Tld\TldServer;

$whois = Whois::create();

// Add default servers
$matchedServers = $whois->getTldModule()
    ->addServers(TldServer::fromDataList([
        ['zone' => '.*.net', 'host' => 'localhost'],
        ['zone' => '.uk.*', 'host' => 'localhost'],
        ['zone' => '.*', 'host' => 'localhost'],
    ]))
    ->matchServers('some.uk.net');

foreach ($matchedServers as $s) {
    echo "{$s->getZone()}  {$s->getHost()}\n";
}

// Matched servers + custom defaults:
//
// .uk.net  whois.centralnic.com
// .uk.net  whois.centralnic.net
// .uk.*  localhost
// .*.net  localhost
// .net  whois.crsnic.net
// .net  whois.verisign-grs.com
// .*  localhost
```

### ASN lookup

##### How to get summary using ASN number:
```php
<?php

use Iodev\Whois\Whois;

$whois = Whois::create();

// Getting raw-text lookup
$response = $whois->lookupAsn("AS32934");
print $response->getText();

// Getting parsed ASN info
$info = $whois->loadAsnInfo("AS32934");
foreach ($info->getRoutes() as $route) {
    print_r([
        'route IPv4' => $route->getRoute(),
        'route IPv6' => $route->getRoute6(),
        'description' => $route->getDescr(),
    ]);   
}

```

### Response caching
Some TLD hosts are very limited for frequent requests. Use cache if in your case requests are repeating.
```php
<?php

use Iodev\Whois\Whois;
use Iodev\Whois\Loaders\SocketLoader;
use Iodev\Whois\Loaders\MemcachedLoader;

$m = new Memcached();
$m->addServer('127.0.0.1', 11211);
$loader = new MemcachedLoader(new SocketLoader(), $m);

$whois = Whois::create($loader);
// do something...
```

## Contributing

The project is open for pull requests, issues and feedback. Please read the CODE_OF_CONDUCT.md
