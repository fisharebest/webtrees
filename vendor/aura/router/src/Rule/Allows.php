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
 * A rule for HTTP methods.
 *
 * @package Aura.Router
 *
 */
class Allows implements RuleInterface
{
    /**
     *
     * Does the server request method match an allowed route method?
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
        if (! $route->allows) {
            return true;
        }

        $requestMethod = $request->getMethod() ?: 'GET';
        return in_array($requestMethod, $route->allows);
    }
}
