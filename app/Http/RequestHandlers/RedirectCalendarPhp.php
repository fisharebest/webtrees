<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectCalendarPhp implements RequestHandlerInterface
{
    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged      = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $cal      = Validator::queryParams($request)->string('cal', '');
        $day      = Validator::queryParams($request)->string('day', '');
        $month    = Validator::queryParams($request)->string('month', '');
        $year     = Validator::queryParams($request)->string('year', '');
        $filterev = Validator::queryParams($request)->string('filterev', '');
        $filterof = Validator::queryParams($request)->string('filterof', '');
        $filtersx = Validator::queryParams($request)->string('filtersx', '');
        $view     = Validator::queryParams($request)->string('view', 'day');
        $tree     = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $url = route(CalendarPage::class, [
                'tree'     => $tree->name(),
                'view'     => $view,
                'cal'      => $cal,
                'day'      => $day,
                'month'    => $month,
                'year'     => $year,
                'filterev' => $filterev,
                'filterof' => $filterof,
                'filtersx' => $filtersx,
            ]);

            return Registry::responseFactory()
                ->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY)
                ->withHeader('Link', '<' . $url . '>; rel="canonical"');
        }

        throw new HttpGoneException();
    }
}
