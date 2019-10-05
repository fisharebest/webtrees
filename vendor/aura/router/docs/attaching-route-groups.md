# Attaching Route Groups

You can add a series of routes under a single master path in the _Map_ using the `Map::attach()` method with a name prefix, path prefix, and a callable to perform the attachment logic.  The callable must take a _Map_ as its only argument.

For example, if you want all your blog-related routes to be mounted at `/blog` in your application, and all of their names to be prefixed with `blog.`, you can do the following:

```php
<?php
$namePrefix = 'blog.';
$pathPrefix = '/blog';
$map->attach($namePrefix, $pathPrefix, function ($map) {

    $map->tokens([
        'id'     => '\d+',
        'format' => '(\.json|\.atom|\.html)?'
    ]);

    $map->defaults([
        'format' => '.html',
    ]);

    $map->get('browse', '');
    $map->get('read', '/{id}{format}');
    $map->patch('edit', '/{id}');
    $map->put('add', '');
    $map->delete('delete', '/{id}');
});
?>
```

Each of the route names will be prefixed with 'blog.', and each of the route paths
will be prefixed with `/blog`, so the effective route names and paths become:

- `blog.browse  =>  /blog`
- `blog.read    =>  /blog/{id}{format}`
- `blog.edit    =>  /blog/{id}`
- `blog.add     =>  /blog/{id}`
- `blog.delete  =>  /blog/{id}`

Any defaults you set on the `$map` inside the callable will revert to their previous values when the callable is complete.
