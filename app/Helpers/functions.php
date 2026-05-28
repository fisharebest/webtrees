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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Session as WebtreesSession;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\View as WebtreesView;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Generate a URL to an asset file in the public folder.
 * Add a version parameter for cache-busting.
 */
function asset(string $path): string
{
    if (str_ends_with($path, '/')) {
        $version = '';
    } elseif (Webtrees::STABILITY === '') {
        $version = '?v=' . Webtrees::VERSION;
    } else {
        $version = '?v=' . filemtime(Webtrees::ROOT_DIR . 'public/' . $path);
    }

    $request  = Registry::container()->get(ServerRequestInterface::class);
    $base_url = Validator::attributes($request)->string('base_url');

    return $base_url . '/public/' . $path . $version;
}

/**
 * Generate a CSRF token form field.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(WebtreesSession::getCsrfToken()) . '">';
}

/**
 * Get the CSRF token value.
 */
function csrf_token(): string
{
    return WebtreesSession::getCsrfToken();
}

function redirect(string $url, int $code = StatusCodeInterface::STATUS_FOUND): ResponseInterface
{
    $response_factory = Registry::container()->get(ResponseFactoryInterface::class);

    return $response_factory
        ->createResponse($code)
        ->withHeader('location', $url);
}

/**
 * Create a response.
 *
 * @param array<mixed>|object|string $content
 * @param array<string>              $headers
 */
function response(array|object|string $content = '', int $code = StatusCodeInterface::STATUS_OK, array $headers = []): ResponseInterface
{
    return Registry::responseFactory()->response($content, $code, $headers);
}

/**
 * Generate a URL for a named route.
 *
 * @param array<bool|int|string|array<string>|null> $parameters
 */
function route(string $route_name, array $parameters = []): string
{
    return Registry::routeFactory()->route($route_name, $parameters);
}

/**
 * Create and render a view in a single operation.
 *
 * @param array<mixed> $data
 */
function view(string $name, array $data = []): string
{
    return WebtreesView::make($name, $data);
}
