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

namespace Fisharebest\Webtrees\Contracts;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * ake a PSR-7 response (using a PSR-17 response factory).
 */
interface ResponseFactoryInterface
{
    /**
     * Redirect to a named route.
     *
     * @param array<bool|int|string|array<string>|null> $parameters
     */
    public function redirect(
        string $route_name,
        array $parameters = [],
        HttpStatusCode $status = HttpStatusCode::Found
    ): ResponseInterface;

    /**
     * Redirect to a URL.
     */
    public function redirectUrl(UriInterface|string $url, HttpStatusCode $code = HttpStatusCode::Found): ResponseInterface;

    /**
     * @param string|array<mixed>|object $content
     * @param array<string,string>       $headers
     */
    public function response(string|array|object $content = '', HttpStatusCode $code = HttpStatusCode::OK, array $headers = []): ResponseInterface;

    /**
     * Create and render a view, and embed it in an HTML page.
     *
     * @param array<string,mixed> $view_data
     */
    public function view(
        string $view_name,
        array $view_data,
        HttpStatusCode $status = HttpStatusCode::OK,
        string $layout_name = Webtrees::LAYOUT_DEFAULT
    ): ResponseInterface;
}
