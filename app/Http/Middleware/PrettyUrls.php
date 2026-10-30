<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_first;
use function http_build_query;
use function is_string;

final readonly class PrettyUrls implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // For sites that do not support URL rewriting, we store the path of the "pretty" URL in
        // a query parameter "route".  This path includes any subfolder.
        $query = $request->getQueryParams();
        $route = $query['route'] ?? null;

        if (is_string($route)) {
            // We generate URLs with the route parameter first.
            if (array_key_first($query) !== 'route') {
                throw new HttpBadRequestException();
            }

            // Convert the ugly URL into a pretty URL.
            unset($query['route']);
            $uri = $request->getUri()
                ->withQuery(http_build_query($query))
                ->withPath($route);

            // Are we generating pretty URLs?
            $rewrite_urls = (bool) $request->getAttribute('rewrite_urls', false);

            if ($rewrite_urls) {
                // We are creating pretty URLs, but received an ugly one. Probably an old bookmark. Redirect it.
                return Registry::responseFactory()
                    ->redirectUrl($uri, HttpStatusCode::PermanentRedirect)
                    ->withHeader('Link', '<' . $uri . '>; rel="canonical"');
            }

            // Continue processing with a pretty URL.
            $request = $request->withUri($uri);
        } elseif ($route !== null) {
            // Parameter present, but not a string? Could be an array? Ignore it.
            unset($query['route']);
            $uri     = $request->getUri()->withQuery(http_build_query($query));
            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}
