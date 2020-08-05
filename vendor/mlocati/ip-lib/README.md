[![TravisCI Build Status](https://api.travis-ci.org/mlocati/ip-lib.svg?branch=master)](https://travis-ci.org/mlocati/ip-lib)
[![AppVeyor Build Status](https://ci.appveyor.com/api/projects/status/github/mlocati/ip-lib?branch=master&svg=true)](https://ci.appveyor.com/project/mlocati/ip-lib)
[![Coding Style checks status](https://github.com/mlocati/ip-lib/workflows/coding%20style/badge.svg)](https://github.com/mlocati/ip-lib/actions?query=workflow%3A%22coding+style%22)
[![Coverage Status](https://coveralls.io/repos/github/mlocati/ip-lib/badge.svg?branch=master)](https://coveralls.io/github/mlocati/ip-lib?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mlocati/ip-lib/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mlocati/ip-lib/?branch=master)
![Packagist Downloads](https://img.shields.io/packagist/dm/mlocati/ip-lib)

# IPLib - Handle IPv4, IPv6 and IP ranges

## Introduction

This library can handle IPv4, IPv6 addresses, as well as IP ranges, in CIDR formats (like `::1/128` or `127.0.0.1/32`) and in pattern format (like `::*:*` or `127.0.*.*`).

## Requirements

The only requirement is PHP 5.3.3.
__No external dependencies__ and __no special PHP configuration__ are needed (yes, it will __always work__ even if PHP has not been built with IPv6 support!).

## Manual installation

[Download](https://github.com/mlocati/ip-lib/releases) the latest version, unzip it and add these lines in our PHP files:

```php
require_once 'path/to/iplib/ip-lib.php';
```

## Installation with Composer

Simply run `composer require mlocati/ip-lib`, or add these lines to your `composer.json` file:

```json
"require": {
    "mlocati/ip-lib": "^1"
}
```

## Sample usage

### Parse an address

To parse an IPv4 address:

```php
$address = \IPLib\Address\IPv4::fromString('127.0.0.1');
```

To parse an IPv6 address:

```php
$address = \IPLib\Address\IPv6::fromString('::1');
```

To parse an address in any format (IPv4 or IPv6):

```php
$address = \IPLib\Factory::addressFromString('::1');
$address = \IPLib\Factory::addressFromString('127.0.0.1');
```

### Get the next/previous addresses

```php
$address = \IPLib\Factory::addressFromString('::1');
echo (string) $address->getPreviousAddress();
// prints ::
echo (string) $address->getNextAddress();
// prints ::2
```

### Parse an IP address range

To parse a subnet (CIDR) range:

```php
$range = \IPLib\Range\Subnet::fromString('127.0.0.1/24');
$range = \IPLib\Range\Subnet::fromString('::1/128');
```

To parse a pattern (asterisk notation) range:

```php
$range = \IPLib\Range\Pattern::fromString('127.0.0.*');
$range = \IPLib\Range\Pattern::fromString('::*');
```

To parse an andress as a range:

```php
$range = \IPLib\Range\Single::fromString('127.0.0.1');
$range = \IPLib\Range\Single::fromString('::1');
```

To parse a range in any format:

```php
$range = \IPLib\Factory::rangeFromString('127.0.0.*');
$range = \IPLib\Factory::rangeFromString('::1/128');
$range = \IPLib\Factory::rangeFromString('::');
```

### Retrive a range from its boundaries

```php
$range = \IPLib\Factory::rangeFromBoundaries('192.168.0.1', '192.168.255.255');
echo (string) $range;
// prints 192.168.0.0/16
```

### Retrive the boundaries of a range

```php
$range = \IPLib\Factory::rangeFromString('127.0.0.*');
echo (string) $range->getStartAddress();
// prints 127.0.0.0
echo (string) $range->getEndAddress();
// prints 127.0.0.255
```

### Format addresses and ranges

Both IP addresses and ranges have a `toString` method that you can use to retrieve a textual representation:
 
```php
echo \IPLib\Factory::addressFromString('127.0.0.1')->toString();
// prints 127.0.0.1
echo \IPLib\Factory::addressFromString('127.000.000.001')->toString();
// prints 127.0.0.1
echo \IPLib\Factory::addressFromString('::1')->toString();
// prints ::1
echo \IPLib\Factory::addressFromString('0:0::1')->toString();
// prints ::1
echo \IPLib\Factory::rangeFromString('0:0::1/64')->toString();
// prints ::1/64
```

When working with IPv6, you may want the full (expanded) representation of the addresses. In this case, simply use a `true` parameter for the `toString` method:

```php
echo \IPLib\Factory::addressFromString('::')->toString(true);
// prints 0000:0000:0000:0000:0000:0000:0000:0000
echo \IPLib\Factory::addressFromString('::1')->toString(true);
// prints 0000:0000:0000:0000:0000:0000:0000:0001
echo \IPLib\Factory::addressFromString('fff::')->toString(true);
// prints 0fff:0000:0000:0000:0000:0000:0000:0000
echo \IPLib\Factory::addressFromString('::0:0')->toString(true);
// prints 0000:0000:0000:0000:0000:0000:0000:0000
echo \IPLib\Factory::addressFromString('1:2:3:4:5:6:7:8')->toString(true);
// prints 0001:0002:0003:0004:0005:0006:0007:0008
echo \IPLib\Factory::rangeFromString('0:0::1/64')->toString();
// prints 0000:0000:0000:0000:0000:0000:0000:0001/64
```

### Check if an address is contained in a range

All the range types offer a `contains` method, and all the IP address types offer a `matches` method: you can call them to check if an address is contained in a range:

```php
$address = \IPLib\Factory::addressFromString('1:2:3:4:5:6:7:8');
$range = \IPLib\Factory::rangeFromString('0:0::1/64');

$contained = $address->matches($range);
// that's equivalent to
$contained = $range->contains($address);
```

Please remark that if the address is IPv4 and the range is IPv6 (or vice-versa), the result will always be `false`.

### Check if a range contains another range

All the range types offer a `containsRange` method: you can call them to check if an address range fully contains another range:

```php
$range1 = \IPLib\Factory::rangeFromString('0:0::1/64');
$range2 = \IPLib\Factory::rangeFromString('0:0::1/65');
$contained = $range1->containsRange($range2);
```

### Getting the type of an IP address

If you want to know if an address is within a private network, or if it's a public IP, or whatever you want, you can use the `getRangeType` method:

```php
$address = \IPLib\Factory::addressFromString('::');

$typeID = $address->getRangeType();

$typeName = \IPLib\Range\Type::getName();
```

The most notable values of the range type ID are:
- `\IPLib\Range\Type::T_UNSPECIFIED` if the address is all zeros (`0.0.0.0` or `::`)
- `\IPLib\Range\Type::T_LOOPBACK` if the address is the localhost (usually `127.0.0.1` or `::1`)
- `\IPLib\Range\Type::T_PRIVATENETWORK` if the address is in the local network (for instance `192.168.0.1` or `fc00::1`)
- `\IPLib\Range\Type::T_PUBLIC` if the address is for public usage (for instance `104.25.25.33` or `2001:503:ba3e::2:30`)

### Getting the type of an IP address range

If you want to know the type of an address range, you can use the `getRangeType` method:

```php
$range = \IPLib\Factory::rangeFromString('2000:0::1/64');
$type = $range->getRangeType();
// $type is \IPLib\Range\Type::T_PUBLIC
echo \IPLib\Range\Type::getName($type);
// 'Public address'
```

Please remark that if a range spans across multiple range types, you'll get NULL as the range type:

```php
$range = \IPLib\Factory::rangeFromString('::/127');
$type = $range->getRangeType();
// $type is null
echo \IPLib\Range\Type::getName($type);
// 'Unknown type'
```

### Converting IP addresses

This library supports converting IPv4 to/from IPv6 addresses using the [6to4 notation](https://tools.ietf.org/html/rfc3056) or the [IPv4-mapped notation](https://tools.ietf.org/html/rfc4291#section-2.5.5.2):

```php
$ipv4 = \IPLib\Factory::addressFromString('1.2.3.4');

// 6to4 notation
$ipv6 = $ipv4->toIPv6();
// This will print "2002:102:304::"
echo (string) $ipv6;
// This will print "1.2.3.4"
echo $ipv6->toIPv4();

// IPv4-mapped notation
$ipv6 = $ipv4->toIPv6IPv4Mapped();
// This will print "::ffff:1.2.3.4"
echo (string) $ipv6;
// This will print "1.2.3.4"
echo $ipv6_6to4->toIPv4();
```

### Converting IP ranges

This library supports IPv4/IPv6 ranges in pattern format (eg. `192.168.*.*`) and in CIDR/subnet format (eg. `192.168.0.0/16`), and it offers a way to convert between the two formats:

```php
// This will print ::*:*:*:*
echo \IPLib\Factory::rangeFromString('::/64')->asPattern()->toString();

// This will print 1:2::/96
echo \IPLib\Factory::rangeFromString('1:2::*:*')->asSubnet()->toString();

// This will print 192.168.0.0/24
echo \IPLib\Factory::rangeFromString('192.168.0.*')->asSubnet()->toString();

// This will print 10.*.*.*
echo \IPLib\Factory::rangeFromString('10.0.0.0/8')->asPattern()->toString();
```

### Getting the subnet mask for IPv4 ranges

You can use the `getSubnetMask()` to get the subnet mask for IPv4 ranges:

```php
// This will print 255.255.255.0
echo \IPLib\Factory::rangeFromString('192.168.0.*')->getSubnetMask()->toString();

// This will print 255.255.255.252
echo \IPLib\Factory::rangeFromString('192.168.0.12/30')->getSubnetMask()->toString();
```

### Getting the reverse DNS lookup address

In order to perform reverse DNS queries, you need to use a special format of the IP addresses.

You can use the `getReverseDNSLookupName()` method of the IP address instances to easily retrieve it:

```php

$ipv4 = \IPLib\Factory::addressFromString('1.2.3.255');
// This will print 255.3.2.1.in-addr.arpa
echo $ipv4->getReverseDNSLookupName();

$ipv6 = \IPLib\Factory::addressFromString('1234:abcd::cafe:babe');
// This will print e.b.a.b.e.f.a.c.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.d.c.b.a.4.3.2.1.ip6.arpa
echo $ipv6->getReverseDNSLookupName();
```

### Using a database

This package offers a great feature: you can store address ranges in a database table, and check if an address is contained in one of the saved ranges with a simple query.

To save a range, you need to store the address type (for IPv4 it's `4`, for IPv6 it's `6`), as well as two values representing the start and the end of the range.
These methods are:
```php
$range->getAddressType();
$range->getComparableStartString();
$range->getComparableEndString();
```

Let's assume that you saved the type in a field called `addressType`, and the range boundaries in two fields called `rangeFrom` and `rangeTo`.

When you want to check if an address is within a stored range, simply use the `getComparableString` method of the address and check if it's between the fields `rangeFrom` and `rangeTo`, and check if the stored `addressType` is the same as the one of the address instance you want to check.

Here's a sample code:

```php
/*
 * Let's assume that:
 * - $pdo is a PDO instance
 * - $range is a range object
 * - $address is an address object
 */

// Save the $range object
$insertQuery = $pdo->prepare('
    insert into ranges (addressType, rangeFrom, rangeTo)
    values (:addressType, :rangeFrom, :rangeTo)
');
$insertQuery->execute(array(
    ':addressType' => $range->getAddressType(),
    ':rangeFrom' => $range->getComparableStartString(),
    ':rangeTo' => $range->getComparableEndString(),
));

// Retrieve the saved ranges where an address $address falls:
$searchQuery = $pdo->prepare('
    select * from ranges
    where addressType = :addressType
    and :address between rangeFrom and rangeTo
');
$searchQuery->execute(array(
    ':addressType' => $address->getAddressType(),
    ':address' => $address->getComparableString(),
));
$rows = $searchQuery->fetchAll();
$searchQuery->closeCursor();
```

## Non decimal notation

IPv4 addresses are usually expresses in decimal notation, for example `192.168.0.1`.

By the way, for historical reasons, widely used libraries (and browsers) accept IPv4 addresses with numbers in octal and/or hexadecimal format.
So, for example, these addresses are all equivalent to `192.168.0.1`:

- `0xC0.0xA8.0x0.0x01` (only hexadecimal)
- `0300.0250.00.01` (only octal)
- `192.0250.0.0x01` (decimal, octal and hexadecimal numbers)

(try it: if you browse to [`http://0177.0.0.0x1`](http://0177.0.0.0x1), your browser will try to browse `http://127.0.0.1`).

This library optionally accepts those alternative syntaxes:

```php
var_export(\IPLib\Factory::addressFromString('0177.0.0.0x1'));
// Prints NULL since by default the library doesn't accept non-decimal addresses

var_export(\IPLib\Factory::addressFromString('0177.0.0.0x1', true, true, false));
// Prints NULL since the fourth argument is false

var_export((string) \IPLib\Factory::addressFromString('0177.0.0.0x1', true, true, true));
// Prints '127.0.0.1' since the fourth argument is true

var_export(\IPLib\Factory::rangeFromString('0177.0.0.0x1/32'));
// Prints NULL since by default the library doesn't accept non-decimal addresses

var_export(\IPLib\Factory::rangeFromString('0177.0.0.0x1/32', false));
// Prints NULL since the second argument is false

var_export((string) \IPLib\Factory::rangeFromString('0177.0.0.0x1/32', true));
// Prints '127.0.0.1/32' since the second argument is true
```

## Do you want to really say thank you?

You can offer me a [monthly coffee](https://github.com/sponsors/mlocati) or a [one-time coffee](https://paypal.me/mlocati) :wink:
