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

/**
 * An example module to demonstrate middleware.
 */
return new class extends AbstractModule implements ModuleCustomInterface, MiddlewareInterface
{
    use ModuleCustomTrait;

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
     * For a description of request and response objects, refer to the Symfony HttpFoundation documentation.
     *
     * @see https://symfony.com/doc/current/components/http_foundation.html
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Code here is executed before we process the request/response.
        // We can prevent the request being executed by throwing an exception.

        $blacklist = [];
        $ip_address = $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';

        if (in_array($ip_address, $blacklist, true)) {
            throw new AccessDeniedHttpException();
        }

        // Generate the response from the next middleware handler.
        $response = $handler->handle($request);

        // Code here is executed after we process the request/response.
        // We can modify the response.
        $response = $response->withHeader('X-Powered-By', 'Fish');

        return $response;
    }
};
