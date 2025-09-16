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
    const REGEX = '#{\s*([a-zA-Z_][a-zA-Z0-9_-]*)\s*:*\s*([^{}]*{*[^{}]*}*[^{}]*)\s*}#';
    const OPT_REGEX = '#{\s*/\s*([a-z][a-zA-Z0-9_-]*\s*:*\s*[^/]*{*[^/]*}*[^/]*,*)}#';
    const EXPLODE_REGEX = '#\s*([a-zA-Z_][a-zA-Z0-9_-]*)\s*(?::*\s*([^,]*[{\d+,}]*[^,\w\s])[^}]?)?#';

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
     * @var string|null
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
     * @return string A URI path string if the route name is found
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
     * @throws Exception\RouteNotFound
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
     * @return void
     *
     */
    protected function buildUrl()
    {
        $this->url = $this->basepath . $this->route->path;

        $host = $this->route->host;
        if (!$host) {
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
     * @return void
     *
     */
    protected function buildTokenReplacements()
    {
        preg_match_all(self::REGEX, $this->url, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = $match[1];

            if (isset($this->data[$name])) {
                $val = $this->data[$name];
                $token = isset($match[2]) ? $match[2] : null;
                if (isset($this->route->tokens[$name]) && is_string($this->route->tokens[$name])) {
                    // if $token is null use route token
                    $token = $token ?: $this->route->tokens[$name];
                }
                if ($token) {
                    if (!preg_match('~^' . $token . '$~', (string)$val)) {
                        throw new \RuntimeException(sprintf(
                            'Parameter value for [%s] did not match the regex `%s`',
                            $name,
                            $token
                        ));
                    }
                }
                $this->repl[$match[0]] = $this->encode($val);
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

    /**
     *
     * Builds replacements for attributes in the generated path.
     *
     * @return void
     *
     */
    protected function buildOptionalReplacements()
    {
        // replacements for optional attributes, if any
        preg_match(self::OPT_REGEX, $this->url, $matches);
        if (!$matches) {
            return;
        }

        // the optional attribute names in the token
        $names = [];
        preg_match_all(self::EXPLODE_REGEX, $matches[1], $exMatches, PREG_SET_ORDER);
        foreach ($exMatches as $match) {
            $name = $match[1];
            $token = isset($match[2]) ? $match[2] : null;
            if (isset($this->route->tokens[$name]) && is_string($this->route->tokens[$name])) {
                // if $token is null use route token
                $token = $token ?: $this->route->tokens[$name];
            }
            $names[] = $token ? [$name, $token] : $name;
        }

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
            $token = null;
            if (is_array($name)) {
                $token = $name[1];
                $name = $name[0];
            }
            // is there data for this optional attribute?
            if (!isset($this->data[$name])) {
                // options are *sequentially* optional, so if one is
                // missing, we're done
                return $repl;
            }

            $val = $this->data[$name];

            // Check val matching token
            if ($token) {
                if (!preg_match('~^' . $token . '$~', (string)$val)) {
                    throw new \RuntimeException(sprintf(
                        'Parameter value for [%s] did not match the regex `%s`',
                        $name,
                        $token
                    ));
                }
            }
            // encode the optional value
            $repl .= '/' . $this->encode($val);
        }
        return $repl;
    }

    /**
     *
     * Builds a wildcard replacement in the generated path.
     *
     * @return void
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
     * Generate the route without url encoding.
     *
     * @param string $name The route name to look up.
     *
     * @param array $data The data to interpolate into the URI; data keys
     * map to attribute tokens in the path.
     *
     * @return string A URI path string
     *
     * @throws Exception\RouteNotFound
     *
     */
    public function generateRaw($name, array $data = [])
    {
        return $this->build($name, $data, true);
    }
}
