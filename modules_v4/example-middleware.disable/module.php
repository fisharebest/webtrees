<?php

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use function in_array;
use function preg_match;

/**
 * An example module to demonstrate middleware.
 */
return new class extends AbstractModule implements ModuleCustomInterface, MiddlewareInterface {
    use ModuleCustomTrait;

    // Regular-expressions to match unwanted bots.
    private const BAD_USER_AGENTS = [
        '/AhrefsBot/',
        '/MJ12bot/',
        '/SeznamBot/',
    ];

    // List of unwanted IP addresses.
    private const BAD_IP_ADDRESSES = [
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
        $ip_address = $request->getAttribute('client_ip');
        if (in_array($ip_address, self::BAD_IP_ADDRESSES, true)) {
            throw new AccessDeniedHttpException();
        }

        $user_agent = $request->getHeaderLine('HTTP_USER_AGENT');
        foreach (self::BAD_USER_AGENTS as $bad_user_agent) {
            if (preg_match($bad_user_agent, $user_agent)) {
                throw new AccessDeniedHttpException();
            }
        }

        // Generate the response.
        $response = $handler->handle($request);

        // Code here is executed after we process the request/response.
        // We can also modify the response.
        $response = $response->withHeader('X-Powered-By', 'Fish');

        return $response;
    }
};
