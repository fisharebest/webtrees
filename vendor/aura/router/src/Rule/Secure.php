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
 * A rule for HTTPS/SSL/TLS.
 *
 * @package Aura.Router
 *
 */
class Secure implements RuleInterface
{
    /**
     *
     * Checks that the Route `$secure` matches the corresponding server values.
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
        if ($route->secure === null) {
            return true;
        }

        $server = $request->getServerParams();
        $secure = $this->https($server) || $this->port443($server);
        return $route->secure == $secure;
    }

    /**
     *
     * Is HTTPS on?
     *
     * @param array $server The server params.
     *
     * @return bool
     *
     */
    protected function https($server)
    {
        return isset($server['HTTPS'])
            && $server['HTTPS'] == 'on';
    }


    /**
     *
     * Is the request on port 443?
     *
     * @param array $server The server params.
     *
     * @return bool
     *
     */
    protected function port443($server)
    {
        return isset($server['SERVER_PORT'])
            && $server['SERVER_PORT'] == 443;
    }
}
