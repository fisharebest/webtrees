# Generating Paths From Routes

To generate a path from a route so that you can create links, first retrieve the _Generator_ from the _RouterContainer_.

```php
<?php
$generator = $routerContainer->getGenerator();
?>
```

You can then call `Generator::generate()` with the route name and optional attributes to use for named placeholder tokens.

```php
<?php
$path = $generator->generate('blog.read', ['id' => 42]);
$href = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
echo "<a href=\"{$href}\">Blog link</a>";
?>
```

The `generate()` method will URL-encode the placeholder token values automatically. Use the `generateRaw()` method to leave them un-encoded.

If there are named placeholder tokens in path without corresponding attributes, those tokens will *not* be replaced, leaving the placeholder token in the path.

If there are attributes without corresponding tokens, those attributes will not be added to the path.

## Optional Attributes

If you `generate()` a path from a route with optional attributes, the attributes will be filled in if they are present in the data for the link. Remember, the optional attributes are *sequentially* optional, so the attributes will not be filled in after the first missing one:

```php
<?php
$map->route('archive', '/archive{/year,month,day}')
    ->tokens([
        'year'  => '\d{4}',
        'month' => '\d{2}',
        'day'   => '\d{2}'
    ]);

$link = $generator->generate('archive', [
    'year' => '1979',
    'month' => '11',
]); // "/archive/1979/11"
?>
```

## Wildcard Attributes

If you `generate()` a link with wildcard attributes, the wildcard key in the data
will be used for the trailing arbitrary attribute values:

```php
<?php
$map->route('wild_post', '/post/{id}')
    ->wildcard('other');

$link = $generator->generate('wild_post', [
    'id' => '88',
    'other' => [
        'foo',
        'bar',
        'baz',
    ]
]); // "/post/88/foo/bar/baz"
?>
```
