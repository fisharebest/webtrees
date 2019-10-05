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
 * A rule for "Accept" headers.
 *
 * @package Aura.Router
 *
 */
class Accepts implements RuleInterface
{
    /**
     *
     * Check that the request Accept headers match one Route accept value.
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
        if (! $route->accepts) {
            return true;
        }

        $requestAccepts = $request->getHeader('Accept');
        if (! $requestAccepts) {
            return true;
        }

        return $this->matches($route->accepts, $requestAccepts);
    }

    /**
     *
     * Does what the route accepts match what the request accepts?
     *
     * @param array $routeAccepts What the route accepts.
     *
     * @param array $requestAccepts What the request accepts.
     *
     * @return bool
     *
     */
    protected function matches($routeAccepts, $requestAccepts)
    {
        $requestAccepts = implode(';', $requestAccepts);
        if ($this->match('*/*', $requestAccepts)) {
            return true;
        }

        foreach ($routeAccepts as $type) {
            if ($this->match($type, $requestAccepts)) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * Is the Accept header a match?
     *
     * @param string $type The Route accept type.
     *
     * @param string $header The Request accept header.
     *
     * @return bool True on a match, false if not.
     *
     */
    protected function match($type, $header)
    {
        list($type, $subtype) = explode('/', $type);
        $type = preg_quote($type);
        $subtype = preg_quote($subtype);
        $regex = "#$type/($subtype|\*)(;q=(\d\.\d))?#";

        $found = preg_match($regex, $header, $matches);
        if (! $found) {
            return false;
        }

        if (isset($matches[3])) {
            return $matches[3] !== '0.0';
        }

        return true;
    }
}
