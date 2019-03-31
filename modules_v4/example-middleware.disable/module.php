<?php

namespace MyCustomNamespace;

use Closure;
use Fisharebest\Webtrees\MiddlewareInterface;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\RequestHandlerInterface;
use Fisharebest\Webtrees\ResponseInterface;
use Fisharebest\Webtrees\ServerRequestInterface;
use Throwable;

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
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Code here is executed before we process the request/response.
        // We can prevent the request being executed by throwing an exception.

        // Generate the response from the request.
        $response = $handler->handle($request);

        // Code here is executed after we process the request/response.
        $response->headers->set('X-Powered-By', 'Fish');

        return $response;
    }
};
