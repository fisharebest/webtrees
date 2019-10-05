# Other Topics

## Catchall Routes

You can use optional placeholder tokens to create a generic catchall route:

```php
<?php
$map->get('catchall', '{/controller,action,id}')
    ->defaults([
        'controller' => 'index',
        'action' => 'browse',
        'id' => null,
    ]);
?>
```

That will match these paths, with these attribute values:

- `/           : ['controller' => 'index', 'action' => 'browse', 'id' => null]`
- `/foo        : ['controller' => 'foo',   'action' => 'browse', 'id' => null]`
- `/foo/bar    : ['controller' => 'foo',   'action' => 'bar',    'id' => null]`
- `/foo/bar/42 : ['controller' => 'foo',   'action' => 'bar',    'id' => '42']`

Because routes are matched in the order they are added, the catchall should be the last route in the _Map_ so that more specific routes may match first.

## Logging

You may wish to log the _Matcher_ activity using a PSR-3 compliant logger. You can tell the _RouterContainer_ how to create a logger instance by passing a factory callable to `setLoggerFactory()`.

```php
<?php
$routerContainer->setLoggerFactory(function () {
    return new Psr3Logger();
});
?>
```

The _RouterContainer_ will use that callable to create the logger and inject it into the _Matcher_. You can then review the debug-level logs for _Matcher_ activity.

## Base Path

The router assumes that all URL paths begin at the top document root, but sometimes you will need them to begin in a subdirectory. In that case, you can instantiate the _RouterContainer_ with an explicit base path; this base path will be used as a prefix for all route matching and path generation.

```php
<?php
// create a container with a base path
$routerContainer = new RouterContainer('/path/to/subdir');

// define a route as normal
$map = $routerContainer->getMap();
$map->get('blog.read', '/blog/{id}', ...);

// if the incoming request is for "/path/to/subdir/blog/{id}"
// then the route will match.
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

// generating a path from the route will add the base path automatically
$generator = $routerContainer->getGenerator();
$path = $generator->generate('blog.read', ['id' => 88]);
echo $path; // "/path/to/subdir/blog/88"
?>
```

## Route Helpers

You may wish to be able to generate route URLs from your views. _Helper\Route_ and Helper\RouteRaw are available to assist you in creating these URLs.

The invocation of the helpers accept the same parameters as the _Generator_  `generate()` method.

```php
<?php
// create a container with a base path
$routerContainer = new RouterContainer('/path/to/subdir');

// define a route as normal
$map = $routerContainer->getMap();
$map->get('blog.read', '/blog/{slug}', ...);

// get the helper from the container
$routeHelper = $routerContainer->newRouteHelper();

// use the helper in a view; note how the URL is escaped
echo $routeHelper('blog.read', [
    'slug' => 'my title'
]); // "/path/to/subdir/blog/my%20title"

// get the raw helper from the container
$routeRawHelper = $routerContainer->newRouteRawHelper();

// use the raw helper; note that no URL escaping is applied
echo $routeRawHelper('blog.read', [
    'slug' => 'my title'
]); // "/path/to/subdir/blog/my title"
```
