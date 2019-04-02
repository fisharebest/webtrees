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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Application;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\View as WebtreesView;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
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
        return Application::getInstance();
    }

    return Application::getInstance()->make($abstract);
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
    if (Webtrees::STABILITY === '') {
        $version = Webtrees::VERSION;
    } else {
        $version = filemtime(WT_ROOT . 'public/' . $path);
    }

    return 'public/' . $path . '?v=' . $version;
}

/**
 * Generate a CSRF token form field.
 *
 * @return string
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . e(Session::getCsrfToken()) . '">';
}

/**
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token()
{
    return Session::getCsrfToken();
}

/**
 * @param string $url
 * @param int    $code
 *
 * @return ResponseInterface
 */
function redirect(string $url, $code = StatusCodeInterface::STATUS_FOUND): ResponseInterface
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
function response($content = '', $code = StatusCodeInterface::STATUS_OK, $headers = []): ResponseInterface
{
    if ($headers === []) {
        if (is_string($content)) {
            $headers = [
                'Content-type'   => 'text/html; charset=utf-8',
                'Content-length' => strlen($content),
            ];
        } else {
            $content = json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
            $headers = [
                'Content-type'   => 'application/json',
                'Content-length' => strlen($content),
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
 * @param string $route
 * @param array  $parameters
 * @param bool   $absolute
 *
 * @return string
 */
function route(string $route, array $parameters = [], bool $absolute = true): string
{
    $parameters = ['route' => $route] + $parameters;

    if ($absolute) {
        return Html::url(WT_BASE_URL . 'index.php', $parameters);
    }

    return Html::url('index.php', $parameters);
}

/**
 * Cerate and render a view in a single operation.
 *
 * @param string  $name
 * @param mixed[] $data
 *
 * @return string
 */
function view(string $name, array $data = [])
{
    return WebtreesView::make($name, $data);
}
