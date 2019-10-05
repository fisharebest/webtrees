<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router;

/**
 *
 * Generates URL paths from routes.
 *
 * @package Aura.Router
 *
 */
class Generator
{
    /**
     *
     * The map of all routes.
     *
     * @var Map
     *
     */
    protected $map;

    /**
     *
     * The route from which the URL is being generated.
     *
     * @var Route
     *
     */
    protected $route;

    /**
     *
     * The URL being generated.
     *
     * @var string
     *
     */
    protected $url;

    /**
     *
     * Data being interpolated into the URL.
     *
     * @var array
     *
     */
    protected $data;

    /**
     *
     * Replacement data.
     *
     * @var array
     *
     */
    protected $repl;

    /**
     *
     * Leave values raw?
     *
     * @var bool
     *
     */
    protected $raw;

    /**
     *
     * The basepath to prefix to generated paths.
     *
     * @var string
     *
     */
    protected $basepath;

    /**
     *
     * Constructor.
     *
     * @param Map $map A route collection object.
     *
     * @param string $basepath The basepath to prefix to generated paths.
     *
     */
    public function __construct(Map $map, $basepath = null)
    {
        $this->map = $map;
        $this->basepath = $basepath;
    }

    /**
     *
     * Looks up a route by name, and interpolates data into it to return
     * a URI path.
     *
     * @param string $name The route name to look up.
     *
     * @param array $data The data to interpolate into the URI; data keys
     * map to attribute tokens in the path.
     *
     * @return string|false A URI path string if the route name is found, or
     * boolean false if not.
     *
     * @throws Exception\RouteNotFound
     *
     */
    public function generate($name, array $data = [])
    {
        return $this->build($name, $data, false);
    }

    /**
     *
     * Generate the route without url encoding.
     *
     * @param string $name The route name to look up.
     *
     * @param array $data The data to interpolate into the URI; data keys
     * map to attribute tokens in the path.
     *
     * @return string|false A URI path string if the route name is found, or
     * boolean false if not.
     *
     * @throws Exception\RouteNotFound
     *
     */
    public function generateRaw($name, array $data = [])
    {
        return $this->build($name, $data, true);
    }

    /**
     *
     * Gets the URL for a Route.
     *
     * @param string $name The route name to look up.
     *
     * @param array $data An array of key-value pairs to interpolate into the
     * attribute tokens in the path for the Route.
     *
     * @param bool $raw Leave the data unencoded?
     *
     * @return string
     *
     */
    protected function build($name, array $data, $raw)
    {
        $this->raw = $raw;
        $this->route = $this->map->getRoute($name);
        $this->buildUrl();
        $this->repl = [];
        $this->data = array_merge($this->route->defaults, $data);

        $this->buildTokenReplacements();
        $this->buildOptionalReplacements();
        $this->url = strtr($this->url, $this->repl);
        $this->buildWildcardReplacement();

        return $this->url;
    }

    /**
     *
     * Builds the URL property.
     *
     * @return null
     *
     */
    protected function buildUrl()
    {
        $this->url = $this->basepath . $this->route->path;

        $host = $this->route->host;
        if (! $host) {
            return;
        }
        $this->url = '//' . $host . $this->url;

        $secure = $this->route->secure;
        if ($secure === null) {
            return;
        }
        $protocol = $secure ? 'https:' : 'http:';
        $this->url = $protocol . $this->url;
    }

    /**
     *
     * Builds urlencoded data for token replacements.
     *
     * @return array
     *
     */
    protected function buildTokenReplacements()
    {
        foreach ($this->data as $key => $val) {
            $this->repl["{{$key}}"] = $this->encode($val);
        }
    }

    /**
     *
     * Builds replacements for attributes in the generated path.
     *
     * @return string
     *
     */
    protected function buildOptionalReplacements()
    {
        // replacements for optional attributes, if any
        preg_match('#{/([a-z][a-zA-Z0-9_,]*)}#', $this->url, $matches);
        if (! $matches) {
            return;
        }

        // the optional attribute names in the token
        $names = explode(',', $matches[1]);

        // this is the full token to replace in the path
        $key = $matches[0];

        // build the replacement string
        $this->repl[$key] = $this->buildOptionalReplacement($names);
    }

    /**
     *
     * Builds the optional replacement for attribute names.
     *
     * @param array $names The optional replacement names.
     *
     * @return string
     *
     */
    protected function buildOptionalReplacement($names)
    {
        $repl = '';
        foreach ($names as $name) {
            // is there data for this optional attribute?
            if (! isset($this->data[$name])) {
                // options are *sequentially* optional, so if one is
                // missing, we're done
                return $repl;
            }
            // encode the optional value
            $repl .= '/' . $this->encode($this->data[$name]);
        }
        return $repl;
    }

    /**
     *
     * Builds a wildcard replacement in the generated path.
     *
     * @return string
     *
     */
    protected function buildWildcardReplacement()
    {
        $wildcard = $this->route->wildcard;
        if ($wildcard && isset($this->data[$wildcard])) {
            $this->url = rtrim($this->url, '/');
            foreach ($this->data[$wildcard] as $val) {
                $this->url .= '/' . $this->encode($val);
            }
        }
    }

    /**
     *
     * Encodes values, or leaves them raw.
     *
     * @param string $val The value to encode or leave raw.
     *
     * @return mixed
     *
     */
    protected function encode($val)
    {
        if ($this->raw) {
            return $val;
        }

        return is_scalar($val) ? rawurlencode($val) : null;
    }
}
