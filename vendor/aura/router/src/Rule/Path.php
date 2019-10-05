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
 * A rule for the URL path.
 *
 * @package Aura.Router
 *
 */
class Path implements RuleInterface
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
     * The regular expression for the path.
     *
     * @var string
     *
     */
    protected $regex;

    /**
     *
     * The basepath to prefix when matching the path.
     *
     * @var string
     *
     */
    protected $basepath;

    /**
     *
     * Constructor.
     *
     * @param string $basepath The basepath to prefix when matching the path.
     *
     */
    public function __construct($basepath = null)
    {
        $this->basepath = $basepath;
    }

    /**
     *
     * Checks that the Request path matches the Route path.
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
        $match = preg_match(
            $this->buildRegex($route),
            $request->getUri()->getPath(),
            $matches
        );

        if (! $match) {
            return false;
        }

        $attributes = $this->getAttributes($matches, $route->wildcard);

        foreach ($this->route->tokens as $name => $pattern) {
            if (is_callable($pattern)) {
                if (!$pattern($attributes[$name], $route, $request)) {
                    return false;
                }
            }
        }

        $route->attributes($attributes);
        return true;
    }

    /**
     *
     * Gets the attributes from the path.
     *
     * @param array $matches The array of matches.
     *
     * @param string $wildcard The name of the wildcard attributes.
     *
     * @return array
     *
     */
    protected function getAttributes($matches, $wildcard)
    {
        // if the path match is exactly an empty string, treat it as unset.
        // this is to support optional attribute values.
        $attributes = [];
        foreach ($matches as $key => $val) {
            if (is_string($key) && $val !== '') {
                $attributes[$key] = rawurldecode($val);
            }
        }

        if (! $wildcard) {
            return $attributes;
        }

        $attributes[$wildcard] = [];
        if (! empty($matches[$wildcard])) {
            $attributes[$wildcard] = array_map(
                'rawurldecode',
                explode('/', $matches[$wildcard])
            );
        }

        return $attributes;
    }

    /**
     *
     * Builds the regular expression for the route path.
     *
     * @param Route $route The Route.
     *
     * @return string
     *
     */
    protected function buildRegex(Route $route)
    {
        $this->route = $route;
        $this->regex = $this->basepath . $this->route->path;
        $this->setRegexOptionalAttributes();
        $this->setRegexAttributes();
        $this->setRegexWildcard();
        $this->regex = '#^' . $this->regex . '$#';
        return $this->regex;
    }

    /**
     *
     * Expands optional attributes in the regex from ``{/foo,bar,baz}` to
     * `(/{foo}(/{bar}(/{baz})?)?)?`.
     *
     * @return null
     *
     */
    protected function setRegexOptionalAttributes()
    {
        preg_match('#{/([a-z][a-zA-Z0-9_,]*)}#', $this->regex, $matches);
        if ($matches) {
            $repl = $this->getRegexOptionalAttributesReplacement($matches[1]);
            $this->regex = str_replace($matches[0], $repl, $this->regex);
        }
    }

    /**
     *
     * Gets the replacement for optional attributes in the regex.
     *
     * @param array $list The optional attributes.
     *
     * @return string
     *
     */
    protected function getRegexOptionalAttributesReplacement($list)
    {
        $list = explode(',', $list);
        $head = $this->getRegexOptionalAttributesReplacementHead($list);
        $tail = '';
        foreach ($list as $name) {
            $head .= "(/{{$name}}";
            $tail .= ')?';
        }

        return $head . $tail;
    }

    /**
     *
     * Gets the leading portion of the optional attributes replacement.
     *
     * @param array $list The optional attributes.
     *
     * @return string
     *
     */
    protected function getRegexOptionalAttributesReplacementHead(&$list)
    {
        // if the optional set is the first part of the path, make sure there
        // is a leading slash in the replacement before the optional attribute.
        $head = '';
        if (substr($this->regex, 0, 2) == '{/') {
            $name = array_shift($list);
            $head = "/({{$name}})?";
        }
        return $head;
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
        if (isset($this->route->tokens[$name]) && is_string($this->route->tokens[$name])) {
            return "(?P<{$name}>{$this->route->tokens[$name]})";
        }

        // use a default subpattern
        return "(?P<{$name}>[^/]+)";
    }

    /**
     *
     * Adds a wildcard subpattern to the end of the regex.
     *
     * @return null
     *
     */
    protected function setRegexWildcard()
    {
        if (! $this->route->wildcard) {
            return;
        }

        $this->regex = rtrim($this->regex, '/')
                     . "(/(?P<{$this->route->wildcard}>.*))?";
    }
}
