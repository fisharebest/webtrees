<?php

/**
 * An example module to demonstrate middleware.
 */

declare(strict_types=1);

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_match;
use function response;

return new class extends AbstractModule implements ModuleCustomInterface, MiddlewareInterface {
    use ModuleCustomTrait;

    // Regular-expressions to match unwanted bots.
    private const BAD_USER_AGENTS = [
        '/AhrefsBot/',
        '/MJ12bot/',
        '/SeznamBot/',
    ];

    // List of unwanted IP ranges in CIDR format, e.g. "123.45.67.89/24".
    private const BAD_IP_RANGES = [
        '127.0.0.1/32',
    ];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'My custom middleware';
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return 'This is an example of middleware';
    }

    /**
     * Code here is executed before and after we process the request/response.
     * We can block access by throwing an exception.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Code here is executed before we process the request/response.

        $ip_address = $request->getAttribute('client-ip');
        foreach (self::BAD_IP_RANGES as $bad_cidr) {
            if ($this->ipInCidr($ip_address, $bad_cidr)) {
                return response('IP address is not allowed: ' . $bad_cidr, 403);
            }
        }

        $user_agent = $request->getHeaderLine('HTTP_USER_AGENT');
        foreach (self::BAD_USER_AGENTS as $bad_user_agent) {
            if (preg_match($bad_user_agent, $user_agent)) {
                return response('User agent is not allowed: ' . $bad_user_agent, 403);
            }
        }

        // Generate the response.
        $response = $handler->handle($request);

        // Code here is executed after we process the request/response.
        // We can also modify the response.
        $response = $response->withHeader('X-Powered-By', 'Fish');

        return $response;
    }

    /**
     * Is an IP address in a CIDR range>
     *
     * @param string $ip
     * @param string $cidr
     *
     * @return bool
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$net, $mask] = explode('/', $cidr);

        $ip_net  = ip2long($net);
        $ip_mask = ~((1 << 32 - (int) $mask) - 1);
        $ip_ip   = ip2long($ip);

        return ($ip_ip & $ip_mask) === ($ip_net & $ip_mask);
    }
};
