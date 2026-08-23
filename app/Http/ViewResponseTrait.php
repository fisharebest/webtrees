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

namespace Fisharebest\Webtrees\Http;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
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
    protected string $layout = 'layouts/default';

    /**
     * @param array<string,mixed> $view_data
     */
    protected function viewResponse(string $view_name, array $view_data, HttpStatusCode $status = HttpStatusCode::OK): ResponseInterface
    {
        $layout_data = [
            // All layouts need these
            'content'          => view($view_name, $view_data),
            'title'            => $view_data['title'],
            // default layout needs these
            'meta_description' => $view_data['meta_description'] ?? null,
            'meta_robots'      => $view_data['meta_robots'] ?? null,
            'modules'          => Registry::container()->get(ModuleService::class)->all(),
            'request'          => Registry::container()->get(ServerRequestInterface::class),
            'theme'            => Registry::container()->get(ModuleThemeInterface::class),
            'tree'             => $view_data['tree'] ?? null,
        ];

        $html = view($this->layout, $layout_data);

        return response($html, $status);
    }
}
