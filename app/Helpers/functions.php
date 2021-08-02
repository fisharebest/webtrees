<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Aura\Router\RouterContainer;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Session as WebtreesSession;
use Fisharebest\Webtrees\View as WebtreesView;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Get the IoC container, or fetch something from it.
 *
 * @param string|null $abstract
 *
 * @return mixed
 */
function app(string $abstract = null)
{
    if ($abstract === null) {
        return Webtrees::container();
    }

    return Webtrees::make($abstract);
}

/**
 * Generate a URL to an asset file in the public folder.
 * Add a version parameter for cache-busting.
 *
 * @param string $path
 *
 * @return string
 */
function asset(string $path): string
{
    if (substr($path, -1) === '/') {
        $version = '';
    } elseif (Webtrees::STABILITY === '') {
        $version = '?v=' . Webtrees::VERSION;
    } else {
        $version = '?v=' . filemtime(Webtrees::ROOT_DIR . 'public/' . $path);
    }

    $base_url = app(ServerRequestInterface::class)->getAttribute('base_url');

    return $base_url . '/public/' . $path . $version;
}

/**
 * Generate a CSRF token form field.
 *
 * @return string
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(WebtreesSession::getCsrfToken()) . '">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token(): string
{
    return WebtreesSession::getCsrfToken();
}

/**
 * @param string $url
 * @param int    $code
 *
 * @return ResponseInterface
 */
function redirect(string $url, int $code = StatusCodeInterface::STATUS_FOUND): ResponseInterface
{
    /** @var ResponseFactoryInterface $response_factory */
    $response_factory = app(ResponseFactoryInterface::class);

    return $response_factory
        ->createResponse($code)
        ->withHeader('Location', $url);
}

/**
 * Create a response.
 *
 * @param mixed    $content
 * @param int      $code
 * @param string[] $headers
 *
 * @return ResponseInterface
 */
function response($content = '', int $code = StatusCodeInterface::STATUS_OK, array $headers = []): ResponseInterface
{
    if ($content === '' && $code === StatusCodeInterface::STATUS_OK) {
        $code = StatusCodeInterface::STATUS_NO_CONTENT;
    }

    if ($headers === []) {
        if (is_string($content)) {
            $headers = [
                'Content-Type' => 'text/html; charset=UTF-8',
            ];
        } else {
            $content = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $headers = [
                'Content-Type' => 'application/json',
            ];
        }
    }

    /** @var ResponseFactoryInterface $response_factory */
    $response_factory = app(ResponseFactoryInterface::class);

    /** @var StreamFactoryInterface $stream_factory */
    $stream_factory = app(StreamFactoryInterface::class);

    $stream = $stream_factory->createStream($content);

    $response = $response_factory
        ->createResponse($code)
        ->withBody($stream);

    foreach ($headers as $key => $value) {
        $response = $response->withHeader($key, $value);
    }

    return $response;
}

/**
 * Generate a URL for a named route.
 *
 * @param string       $route_name
 * @param array<mixed> $parameters
 *
 * @return string
 */
function route(string $route_name, array $parameters = []): string
{
    $request          = app(ServerRequestInterface::class);
    $base_url         = $request->getAttribute('base_url');
    $router_container = app(RouterContainer::class);
    $route            = $router_container->getMap()->getRoute($route_name);

    // Generate the URL.
    $url = $router_container->getGenerator()->generate($route_name, $parameters);

    // Aura ignores parameters that are not tokens.  We need to add them as query parameters.
    $parameters = array_filter($parameters, static function (string $key) use ($route): bool {
        return !str_contains($route->path, '{' . $key . '}') && !str_contains($route->path, '{/' . $key . '}');
    }, ARRAY_FILTER_USE_KEY);

    if ($request->getAttribute('rewrite_urls') === '1') {
        // Make the pretty URL absolute.
        $base_path = parse_url($base_url, PHP_URL_PATH) ?? '';
        $url = $base_url . substr($url, strlen($base_path));
    } else {
        // Turn the pretty URL into an ugly one.
        $path       = parse_url($url, PHP_URL_PATH);
        $parameters = ['route' => $path] + $parameters;
        $url        = $base_url . '/index.php';
    }

    return Html::url($url, $parameters);
}

/**
 * Create and render a view in a single operation.
 *
 * @param string       $name
 * @param array<mixed> $data
 *
 * @return string
 */
function view(string $name, array $data = []): string
{
    return WebtreesView::make($name, $data);
}
