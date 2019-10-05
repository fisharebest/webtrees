<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router;

use ArrayIterator;
use IteratorAggregate;

/**
 *
 * A collection of route objects.
 *
 * @package Aura.Router
 *
 */
class Map implements IteratorAggregate
{
    /**
     *
     * An array of route objects.
     *
     * @var array
     *
     */
    protected $routes = [];

    /**
     *
     * A prototype Route.
     *
     * @var Route
     *
     */
    protected $protoRoute;

    /**
     *
     * Constructor.
     *
     * @param Route $protoRoute A prototype Route.
     *
     */
    public function __construct(Route $protoRoute)
    {
        $this->protoRoute = $protoRoute;
    }

    /**
     *
     * Proxy unknown method calls to the proto-route.
     *
     * @param string $method The method name.
     *
     * @param array $params The method params.
     *
     * @return $this
     *
     */
    public function __call($method, $params)
    {
        call_user_func_array([$this->protoRoute, $method], $params);
        return $this;
    }

    /**
     *
     * IteratorAggregate: returns the iterator object.
     *
     * @return ArrayIterator
     *
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    /**
     *
     * Sets the array of route objects to use.
     *
     * @param array $routes Use this array of routes.
     *
     * @return null
     *
     * @see getRoutes()
     *
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     *
     * Gets the route collection.
     *
     * @return Map
     *
     * @see setRoutes()
     *
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     *
     * Adds a pre-built route to the collection.
     *
     * @param Route $route The pre-built route.
     *
     * @return $this
     *
     * @throws Exception\RouteAlreadyExists when the route name is already
     * mapped.
     *
     */
    public function addRoute(Route $route)
    {
        $name = $route->name;

        if (! $name) {
            $this->routes[] = $route;
            return;
        }

        if (isset($this->routes[$name])) {
            throw new Exception\RouteAlreadyExists($name);
        }

        $this->routes[$name] = $route;
    }

    /**
     *
     * Gets a route by name.
     *
     * @param string $name The route name.
     *
     * @return Route
     *
     */
    public function getRoute($name)
    {
        if (! isset($this->routes[$name])) {
            throw new Exception\RouteNotFound($name);
        }

        return $this->routes[$name];
    }

    /**
     *
     * Adds a generic route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function route($name, $path, $handler = null)
    {
        $route = clone $this->protoRoute;
        $route->name($name);
        $route->path($path);
        $route->handler($handler);
        $this->addRoute($route);
        return $route;
    }

    /**
     *
     * Adds a GET route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function get($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('GET');
        return $route;
    }

    /**
     *
     * Adds a DELETE route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function delete($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('DELETE');
        return $route;
    }

    /**
     *
     * Adds a HEAD route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function head($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('HEAD');
        return $route;
    }

    /**
     *
     * Adds an OPTIONS route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function options($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('OPTIONS');
        return $route;
    }

    /**
     *
     * Adds a PATCH route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function patch($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('PATCH');
        return $route;
    }

    /**
     *
     * Adds a POST route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function post($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('POST');
        return $route;
    }

    /**
     *
     * Adds a PUT route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public function put($name, $path, $handler = null)
    {
        $route = $this->route($name, $path, $handler);
        $route->allows('PUT');
        return $route;
    }

    /**
     *
     * Attaches routes to a specific path prefix, and prefixes the attached
     * route names.
     *
     * @param string $namePrefix The prefix for all route names being attached.
     *
     * @param string $pathPrefix The prefix for all route paths being attached.
     *
     * @param callable $callable A callable that uses the Map to add new
     * routes. Its signature is `function (\Aura\Router\Map $map)`; $this
     * Map instance will be passed to the callable.
     *
     * @return null
     *
     */
    public function attach($namePrefix, $pathPrefix, callable $callable)
    {
        // retain current prototype
        $old = $this->protoRoute;

        // clone a new prototype, update prefixes, and retain it
        $new = clone $old;
        $new->namePrefix($old->namePrefix . $namePrefix);
        $new->pathPrefix($old->pathPrefix . $pathPrefix);
        $this->protoRoute = $new;

        // run the callable and restore the old prototype
        $callable($this);
        $this->protoRoute = $old;
    }
}
