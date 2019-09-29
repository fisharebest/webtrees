<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_flip;
use function array_intersect_key;
use function explode;
use function filter_var;
use function parse_url;
use function preg_replace;
use function trim;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;
use const PHP_URL_HOST;
use const PHP_URL_PASS;
use const PHP_URL_PATH;
use const PHP_URL_PORT;
use const PHP_URL_QUERY;
use const PHP_URL_SCHEME;
use const PHP_URL_USER;

/**
 * Middleware to set the Web environment.
 */
class WebEnvironment implements MiddlewareInterface
{
    // HTTP headers that may contain request information.
    // These should only be used when explicitly trusted.
    private const HEADERS_IP = [
        'CF-Connecting-IP',
        'Client-Ip',
        'Forwarded',
        'True-Client-IP',
        'X-Cluster-Client-Ip',
        'X-Forwarded',
        'X-Forwarded-For',
        'X-ProxyUser-IP',
        'X-Real-IP',
    ];

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $base_url    = $this->extractBaseUrl($request);
        $client_ip   = $this->extractClientIp($request);
        $request_uri = $this->extractRequestUri($request, $base_url);

        $request = $request
            ->withAttribute('base_url', $base_url)
            ->withAttribute('client_ip', $client_ip)
            ->withAttribute('request_uri', $request_uri);

        return $handler->handle($request);
    }

    /**
     * Detect the URL of the HTTP request.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function extractBaseUrl(ServerRequestInterface $request): string
    {
        $base_url = $request->getAttribute('base_url', (string) $request->getUri());
        $base_url = preg_replace('/index\.php.*/', '', $base_url);

        return $base_url;
    }

    /**
     * Detect the client's IP address.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function extractClientIp(ServerRequestInterface $request): string
    {
        $trusted_headers = $this->filterHeaders($request->getServerParams(), self::HEADERS_IP);

        foreach ($trusted_headers as $name => $value) {
            // The "Forwarded" header should be in RFC7239 format, or it may be a simple IP address.
            if ($name === 'Forwarded' && preg_match('/for= *([^; ]+)/', $value, $match)) {
                $ip = $match[1];
            } else {
                // We may have a list of IP addresses: client, proxy-1, proxy-2, ...
                $ip = trim(explode(',', $value)[0]);
            }

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                return $ip;
            }
        }

        return $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Filter potentially useful headers.
     *
     * @param string[] $all_headers
     * @param string[] $trusted_headers
     *
     * @return string[]
     */
    private function filterHeaders(array $all_headers, array $trusted_headers): array
    {
        return array_intersect_key($all_headers, array_flip($trusted_headers));
    }

    /**
     * Detect the request URI.  We cannot reliably detect scheme/host/port, so
     * take these values from the (user-configured) base_url.
     *
     * @param ServerRequestInterface $request
     * @param string                 $base_url
     *
     * @return string
     */
    private function extractRequestUri(ServerRequestInterface $request, string $base_url): string
    {
        $psr7_url = (string) $request->getUri();

        // Scheme, host and port cannot always be detected from the environment.
        // Take these from the user-configured base_url or trusted headers.
        $scheme = (string) parse_url($base_url, PHP_URL_SCHEME);
        $host   = (string) parse_url($base_url, PHP_URL_HOST);
        $port   = (string) parse_url($base_url, PHP_URL_PORT);
        $user   = (string) parse_url($psr7_url, PHP_URL_USER);
        $pass   = (string) parse_url($psr7_url, PHP_URL_PASS);
        $path   = (string) parse_url($psr7_url, PHP_URL_PATH);
        $query  = (string) parse_url($psr7_url, PHP_URL_QUERY);

        $scheme = $scheme === '' ? '' : $scheme . '://';
        $host   = $user === '' && $pass === '' ? $host : '@' . $host;
        $pass   = $pass === '' ? '' : ':' . $pass;
        $query  = $query === '' ? '' : '?' . $query;

        return $scheme . $user . $pass . $host . $port . $path . $query;
    }
}
