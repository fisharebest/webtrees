# Rules for detecting usage of deprecated classes, methods, properties, constants and traits.

[![Build](https://github.com/phpstan/phpstan-deprecation-rules/workflows/Build/badge.svg)](https://github.com/phpstan/phpstan-deprecation-rules/actions)
[![Latest Stable Version](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/v/stable)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)
[![License](https://poser.pugx.org/phpstan/phpstan-deprecation-rules/license)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)

* [PHPStan](https://phpstan.org/)

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev phpstan/phpstan-deprecation-rules
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include rules.neon in your project's PHPStan config:

```
includes:
    - vendor/phpstan/phpstan-deprecation-rules/rules.neon
```
</details>

## Deprecating code you don't own

This extension emits deprecation warnings on code, which uses properties/functions/methods/classes which are annotated as `@deprecated`.

In case you don't own the code which you want to be considered deprecated, use [PHPStan Stub Files](https://phpstan.org/user-guide/stub-files) to declare deprecations for vendor files like:
```
/** @deprecated */
class ThirdPartyClass {}
```
