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

use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\MiddlewareInterface;
use Fisharebest\Webtrees\RedirectResponse;
use Fisharebest\Webtrees\RequestHandlerInterface;
use Fisharebest\Webtrees\ResponseInterface;
use Fisharebest\Webtrees\ServerRequestInterface;

/**
 * Middleware to add debugging info to the PHP debugbar.
 */
class DebugBarData implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (class_exists(DebugBar::class)) {
            // This timer gets stopped automatically when we generate the response.
            DebugBar::startMeasure('controller_action');
            $response = $handler->handle($request);

            if ($response instanceof RedirectResponse) {
                // Show the debug data on the next page
                DebugBar::stackData();
            } elseif ($request->isXmlHttpRequest()) {
                // Use HTTP headers and some jQuery to add debug to the current page.
                DebugBar::sendDataInHeaders();
            }

            return $response;
        }

        return $handler->handle($request);
    }
}
