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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Show anniversaries for events in a given day/month/year.
 */
class CalendarAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route(CalendarPage::class, [
            'tree'     => Validator::attributes($request)->tree()->name(),
            'view'     => Validator::attributes($request)->isInArray(['day', 'month', 'year'])->string('view'),
            'cal'      => Validator::parsedBody($request)->string('cal'),
            'day'      => Validator::parsedBody($request)->integer('day'),
            'month'    => Validator::parsedBody($request)->string('month'),
            'year'     => Validator::parsedBody($request)->integer('year'),
            'filterev' => Validator::parsedBody($request)->string('filterev'),
            'filterof' => Validator::parsedBody($request)->string('filterof'),
            'filtersx' => Validator::parsedBody($request)->string('filtersx'),
        ]));
    }
}
