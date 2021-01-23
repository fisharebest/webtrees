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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function response;
use function view;

/**
 * Allows a page fragment to be embedded in a page layout and converted to an HTTP response.
 * Used by controllers, request-handlers, modules, etc.
 */
trait ViewResponseTrait
{
    /** @var string */
    protected $layout = 'layouts/default';

    /**
     * @param string  $view_name
     * @param mixed[] $view_data
     * @param int     $status
     *
     * @return ResponseInterface
     */
    protected function viewResponse(string $view_name, array $view_data, int $status = StatusCodeInterface::STATUS_OK): ResponseInterface
    {
        // Make the view's data available to the layout.
        $layout_data = $view_data;

        // Render the view
        $layout_data['content'] = view($view_name, $view_data);
        $layout_data['request'] = app(ServerRequestInterface::class);

        // Insert the view into the layout
        $html = view($this->layout, $layout_data);

        return response($html, $status);
    }
}
