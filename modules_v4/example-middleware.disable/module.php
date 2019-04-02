<?php

namespace MyCustomNamespace;

use Closure;
use Fisharebest\Webtrees\Http\Middleware\MiddlewareInterface;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * An example module to demonstrate middleware.
 */
return new class extends AbstractModule implements ModuleCustomInterface, MiddlewareInterface {
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
        return 'This adds a custom HTTP headers to all responses';
    }

    /**
     * For a description of request and response objects, refer to the Symfony HttpFoundation documentation.
     *
     * @see https://symfony.com/doc/current/components/http_foundation.html
     *
     * @param ServerRequestInterface $request
     * @param Closure                $next
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, Closure $next): ResponseInterface
    {
        // Code here is executed before we process the request/response.
        // We can prevent the request being executed by throwing an exception.

        // Generate the response from the request.
        $response = $next($request);

        // Code here is executed after we process the request/response.
        $response->headers->set('X-Powered-By', 'Fish');

        return $response;
    }
};
