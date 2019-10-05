<?php
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

// must match **all** headers
class Headers
{
    /**
     *
     * Checks that header values match their related regular expressions, and
     * captures the headers as attributes.
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
        $routeHeaders = $route->headers;
        if (! $routeHeaders) {
            return true;
        }

        $requestHeaders = $request->getHeaders();
        $attributes = [];
        foreach ($routeHeaders as $name => $regex) {
            $match = $this->match($requestHeaders, $name, $regex);
            if ($match === false) {
                return false;
            }
            $attributes[$name] = $match;
        }

        $route->addAttributes($attributes);
        return true;
    }

    /**
     *
     * Does a header value match a regex?
     *
     * @param $headers The array of all request headers.
     *
     * @param string $name The header name to look for.
     *
     * @param string $regex The regex to match against.
     *
     * @return string The match.
     *
     */
    protected function match($headers, $name, $regex)
    {
        $name = strtolower($name);
        if (! isset($headers[$name])) {
            return false;
        }

        foreach ($headers[$name] as $value) {
            if (preg_match($regex, $value, $matches)) {
                return $value;
            }
        }

        return false;
    }
}
