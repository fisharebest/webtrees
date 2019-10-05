<?php
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

// must match **all** cookies
class Cookies
{
    /**
     *
     * Checks that cookie values match their related regular expressions, and
     * captures the cookies as attributes.
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
        $routeCookies = $route->cookies;
        if (! $routeCookies) {
            return true;
        }

        $requestCookies = $request->getCookieParams();
        $attributes = [];
        foreach ($routeCookies as $name => $regex) {
            $match = $this->match($requestCookies, $name, $regex);
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
     * Does a cookie value match a regex?
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
    protected function match($cookies, $name, $regex)
    {
        if (! isset($cookies[$name])) {
            return false;
        }

        if (preg_match($regex, $cookies[$name], $matches)) {
            return $cookies[$name];
        }

        return false;
    }
}
