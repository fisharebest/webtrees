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

use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SearchPhoneticAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route(SearchPhoneticPage::class, [
            'firstname'    => Validator::parsedBody($request)->string('firstname'),
            'lastname'     => Validator::parsedBody($request)->string('lastname'),
            'place'        => Validator::parsedBody($request)->string('place'),
            'search_trees' => Validator::parsedBody($request)->array('search_trees'),
            'soundex'      => Validator::parsedBody($request)->string('soundex'),
            'tree'         => Validator::attributes($request)->tree()->name(),
        ]));
    }
}
