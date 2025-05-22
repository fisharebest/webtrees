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

use Fisharebest\Webtrees\Services\PhpService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function connection_status;
use function fastcgi_finish_request;
use function header;
use function header_remove;
use function headers_sent;
use function http_response_code;
use function ob_get_length;
use function ob_get_level;
use function sprintf;

use const CONNECTION_NORMAL;

/**
 * Middleware to emit the response - send it back to the webserver.
 */
class EmitResponse implements MiddlewareInterface
{
    // Stream the output in chunks.
    private const int CHUNK_SIZE = 65536;

    public function __construct(private PhpService $php_service)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $this->assertHeadersNotEmitted();
        $this->removeDefaultPhpHeaders();

        // Unless webtrees set a cache-control header, assume the page cannot be cached
        if (!$response->hasHeader('cache-control')) {
            $response = $response->withHeader('cache-control', 'no-store');
        }

        $this->assertBodyNotEmitted();
        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitBody($response);
        $this->closeConnection();

        return $response;
    }

    private function removeDefaultPhpHeaders(): void
    {
        header_remove('X-Powered-By');
        header_remove('cache-control');
        header_remove('Expires');
        header_remove('Pragma');
    }

    private function assertHeadersNotEmitted(): void
    {
        if (headers_sent($file, $line)) {
            $message = sprintf('Headers already sent at %s:%d', $file, $line);

            throw new RuntimeException($message);
        }
    }

    private function assertBodyNotEmitted(): void
    {
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            // The output probably contains an error message.
            $output = ob_get_clean();

            throw new RuntimeException('Output already started: ' . $output);
        }
    }

    private function emitStatusLine(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());

        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(
                    sprintf('%s: %s', $name, $value),
                    false,
                    $response->getStatusCode()
                );
            }
        }
    }

    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof() && connection_status() === CONNECTION_NORMAL) {
            echo $body->read(self::CHUNK_SIZE);
        }
    }

    private function closeConnection(): void
    {
        if ($this->php_service->functionExists(function: 'fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
