# Middleland

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)

Simple (but powerful)
[PSR-15](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-15-request-handlers.md)
middleware dispatcher:

## Requirements

- PHP 7
- A [PSR-7 Message implementation](http://www.php-fig.org/psr/psr-7/), for
  example [laminas-diactoros](https://github.com/laminas/laminas-diactoros)
- Optionally, a [PSR-11 container](https://github.com/php-fig/container)
  implementation to create the middleware components on demand.

## Example

```php
use Middleland\Dispatcher;

$middleware = [
    new Middleware1(),
    new Middleware2(),
    new Middleware3(),

    // A dispatcher can be used to group middlewares
    new Dispatcher([
        new Middleware4(),
        new Middleware5(),
    ]),

    // You can use closures
    function ($request, $next) {
        $response = $next->handle($request);
        return $response->withHeader('X-Foo', 'Bar');
    },

    // Or use a string to create the middleware on demand using a PSR-11 container
    'middleware6'

    // USE AN ARRAY TO ADD CONDITIONS:

    // This middleware is processed only in paths starting by "/admin"
    ['/admin', new MiddlewareAdmin()],

    // This is processed in DEV
    [ENV === 'DEV', new MiddlewareAdmin()],

    // Use callables to create other conditions
    [
        function ($request) {
            return $request->getUri()->getScheme() === 'https';
        },
        new MiddlewareHttps()
    ],

    // There are some matchers included in this library to create conditions
    [
        new Pattern('*.png'),
        new MiddlewareForPngFiles()
    ],

    //And use several for each middleware
    [
        ENV === 'DEV',
        new Pattern('*.png'),
        new MiddlewareForPngFilesInDev()
    ],
];

$dispatcher = new Dispatcher($middleware, new Container());

$response = $dispatcher->dispatch(new Request());
```

## Matchers

As you can see in the example above, you can use an array of "matchers" to
filter the requests that receive middlewares. You can use callables, instances
of `Middleland\Matchers\MatcherInterface` or booleans, but for comodity, the
string values are also used to create `Middleland\Matchers\Path` instances. The
available matchers are:

| Name      | Description                                                                 | Example                                              |
| --------- | --------------------------------------------------------------------------- | ---------------------------------------------------- |
| `Path`    | Filter requests by base path. Use exclamation mark for negative matches     | `new Path('/admin')`, `new Path('!/not-admin')`      |
| `Pattern` | Filter requests by path pattern. Use exclamation mark for negative matches  | `new Pattern('*.png')` `new Pattern('!*.jpg')`       |
| `Accept`  | Filter requests by Accept header. Use exclamation mark for negative matches | `new Accept('text/html')` `new Accept('!image/png')` |

## How to create matchers

Just use a callable or an instance of the
`Middleland\Matchers\MatcherInterface`. Example:

```php
use Middleland\Matchers\MatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class IsAjax implements MatcherInterface
{
    public function __invoke(ServerRequestInterface $request): bool
    {
    	return $request->getHeaderLine('X-Requested-With') === 'xmlhttprequest';
	}
}
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/oscarotero/middleland.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/oscarotero/middleland
