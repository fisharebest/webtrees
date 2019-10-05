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
 * A rule for special matching logic on individual routes.
 *
 * @package Aura.Router
 *
 */
class Special implements RuleInterface
{
    /**
     *
     * Invokes the special matching logic on each individual Route, if any.
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
        $special = $route->special;
        if (! $special) {
            return true;
        }

        return (bool) $special($request, $route);
    }
}
