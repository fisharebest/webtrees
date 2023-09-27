<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
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
    ): ResponseInterface;

    /**
     * Redirect to a URL.
     *
     * @param UriInterface|string $url
     * @param int                 $code
     *
     * @return ResponseInterface
     */
    public function redirectUrl(UriInterface|string $url, int $code = StatusCodeInterface::STATUS_FOUND): ResponseInterface;

    /**
     * @param string|array<mixed>|object $content
     * @param int                        $code
     * @param array<string,string>       $headers
     *
     * @return ResponseInterface
     */
    public function response(string|array|object $content = '', int $code = StatusCodeInterface::STATUS_OK, array $headers = []): ResponseInterface;

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
    ): ResponseInterface;
}
