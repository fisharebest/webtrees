<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function extension_loaded;
use function gzdeflate;
use function gzencode;
use function in_array;
use function str_contains;
use function strstr;
use function strtolower;
use function strtr;

/**
 * Middleware to compress (gzip or deflate) a response.
 */
class CompressResponse implements MiddlewareInterface
{
    // Non-text responses that will benefit from compression.
    protected const MIME_TYPES = [
        'application/javascript',
        'application/json',
        'application/pdf',
        'application/vnd.geo+json',
        'application/xml',
        'image/svg+xml',
    ];

    protected StreamFactoryInterface $stream_factory;

    /**
     * @param StreamFactoryInterface $stream_factory
     */
    public function __construct(StreamFactoryInterface $stream_factory)
    {
        $this->stream_factory = $stream_factory;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $method = $this->compressionMethod($request);

        if ($method !== null && $this->isCompressible($response)) {
            $content = (string) $response->getBody();

            switch ($method) {
                case 'deflate':
                    $content = gzdeflate($content);
                    break;

                case 'gzip':
                    $content = gzencode($content);
                    break;
            }

            if ($content === false) {
                return $response;
            }

            $stream = $this->stream_factory->createStream($content);

            return $response
                ->withBody($stream)
                ->withHeader('content-encoding', $method)
                ->withHeader('vary', 'accept-encoding');
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     *
     * @return string|null
     */
    protected function compressionMethod(RequestInterface $request): ?string
    {
        $accept_encoding = strtolower($request->getHeaderLine('accept-encoding'));
        $zlib_available  = extension_loaded('zlib');

        if ($zlib_available) {
            if (str_contains($accept_encoding, 'gzip')) {
                return 'gzip';
            }

            if (str_contains($accept_encoding, 'deflate')) {
                return 'deflate';
            }
        }

        return null;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    protected function isCompressible(ResponseInterface $response): bool
    {
        // Already encoded?
        if ($response->hasHeader('content-encoding')) {
            return false;
        }

        $content_type = $response->getHeaderLine('content-type');
        $content_type = strtr($content_type, [' ' => '']);
        $content_type = strstr($content_type, ';', true) ?: $content_type;
        $content_type = strtolower($content_type);

        if (str_starts_with($content_type, 'text/')) {
            return true;
        }

        return in_array($content_type, static::MIME_TYPES, true);
    }
}
