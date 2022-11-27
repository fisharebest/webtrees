<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Factories;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\ResponseFactoryInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseFactoryInterface as PSR17ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;

use function app;
use function is_string;
use function json_encode;
use function view;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * Make a PSR-7 response (using a PSR-17 response factory).
 */
class ResponseFactory implements ResponseFactoryInterface
{
    private PSR17ResponseFactoryInterface $response_factory;

    private StreamFactoryInterface $stream_factory;

    /**
     * @param PSR17ResponseFactoryInterface $response_factory
     * @param StreamFactoryInterface        $stream_factory
     */
    public function __construct(PSR17ResponseFactoryInterface $response_factory, StreamFactoryInterface $stream_factory)
    {
        $this->response_factory = $response_factory;
        $this->stream_factory   = $stream_factory;
    }

    /**
     * Redirect to a named route.
     *
     * @param string                                    $route_name
     * @param array<bool|int|string|array<string>|null> $parameters
     * @param int                                       $status
     *
     * @return ResponseInterface
     *
     */
    public function redirect(
        string $route_name,
        array $parameters = [],
        int $status = StatusCodeInterface::STATUS_FOUND
    ): ResponseInterface {
        $url = Registry::routeFactory()->route($route_name, $parameters);

        return $this->redirectUrl($url, $status);
    }

    /**
     * Redirect to a URL.
     *
     * @param UriInterface|string $url
     * @param int                 $code
     *
     * @return ResponseInterface
     */
    public function redirectUrl(UriInterface|string $url, int $code = StatusCodeInterface::STATUS_FOUND): ResponseInterface
    {
        return $this->response_factory
            ->createResponse($code)
            ->withHeader('location', (string) $url);
    }

    /**
     * @param string|array<mixed>|object $content
     * @param int                        $code
     * @param array<string,string>       $headers
     *
     * @return ResponseInterface
     */
    public function response(string|array|object $content = '', int $code = StatusCodeInterface::STATUS_OK, array $headers = []): ResponseInterface
    {
        if ($content === '' && $code === StatusCodeInterface::STATUS_OK) {
            $code = StatusCodeInterface::STATUS_NO_CONTENT;
        }

        if (is_string($content)) {
            $headers['content-type'] ??= 'text/html; charset=UTF-8';
        } else {
            $content                 = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $headers['content-type'] ??= 'application/json';
        }

        $stream = $this->stream_factory->createStream($content);

        $response = $this->response_factory
            ->createResponse($code)
            ->withBody($stream);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }

    /**
     * Create and render a view, and embed it in an HTML page.
     *
     * @param string              $view_name
     * @param array<string,mixed> $view_data
     * @param int                 $status
     * @param string              $layout_name
     *
     * @return ResponseInterface
     */
    public function view(
        string $view_name,
        array $view_data,
        int $status = StatusCodeInterface::STATUS_OK,
        string $layout_name = Webtrees::LAYOUT_DEFAULT
    ): ResponseInterface {
        // Render the view.
        $content = view($view_name, $view_data);

        // Make the view's data available to the layout.
        $layout_data = [
            'content' => $content,
            'request' => app(ServerRequestInterface::class),
            'theme'   => app(ModuleThemeInterface::class),
            'title'   => $view_data['title'] ?? Webtrees::NAME,
        ];

        // Embed the content in the layout.
        $html = view($layout_name, $layout_data);

        return $this->response($html, $status);
    }
}
