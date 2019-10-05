<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * A rule for the HTTP host.
 *
 * @package Aura.Router
 *
 */
class Host implements RuleInterface
{
    /**
     *
     * Use this Route to build the regex.
     *
     * @var Route
     *
     */
    protected $route;

    /**
     *
     * The regular expression for the host.
     *
     * @var string
     *
     */
    protected $regex;

    /**
     *
     * Checks that the Request host matches the Route host.
     *
     * @param ServerRequestInterface $request The HTTP request.
     *
     * @param Route $route The route.
     *
     * @return bool True on success, false on failure.
     *
     */
    public function __invoke(ServerRequestInterface $request, Route $route)
    {
        if (! $route->host) {
            return true;
        }

        $match = preg_match(
            $this->buildRegex($route),
            $request->getUri()->getHost(),
            $matches
        );

        if (! $match) {
            return false;
        }

        $route->attributes($this->getAttributes($matches));
        return true;
    }

    /**
     *
     * Gets the attributes out of the regex matches.
     *
     * @param array $matches The regex matches.
     *
     * @return array
     *
     */
    protected function getAttributes($matches)
    {
        $attributes = [];
        foreach ($matches as $key => $val) {
            if (is_string($key)) {
                $attributes[$key] = $val;
            }
        }
        return $attributes;
    }

    /**
     *
     * Builds the regular expression for the route host.
     *
     * @param Route $route The Route.
     *
     * @return string
     *
     */
    protected function buildRegex(Route $route)
    {
        $this->route = $route;
        $this->regex = str_replace('.', '\\.', $this->route->host);
        $this->setRegexAttributes();
        $this->regex = '#^' . $this->regex . '$#';
        return $this->regex;
    }

    /**
     *
     * Expands attribute names in the regex to named subpatterns; adds default
     * `null` values for attributes without defaults.
     *
     * @return null
     *
     */
    protected function setRegexAttributes()
    {
        $find = '#{([a-z][a-zA-Z0-9_]*)}#';
        $attributes = $this->route->attributes;
        $newAttributes = [];
        preg_match_all($find, $this->regex, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = $match[1];
            $subpattern = $this->getSubpattern($name);
            $this->regex = str_replace("{{$name}}", $subpattern, $this->regex);
            if (! isset($attributes[$name])) {
                $newAttributes[$name] = null;
            }
        }
        $this->route->attributes($newAttributes);
    }

    /**
     *
     * Returns a named subpattern for a attribute name.
     *
     * @param string $name The attribute name.
     *
     * @return string The named subpattern.
     *
     */
    protected function getSubpattern($name)
    {
        // is there a custom subpattern for the name?
        if (isset($this->route->tokens[$name])) {
            return "(?P<{$name}>{$this->route->tokens[$name]})";
        }

        // use a default subpattern, stop at first dot
        return "(?P<{$name}>[^\.]+)";
    }
}
