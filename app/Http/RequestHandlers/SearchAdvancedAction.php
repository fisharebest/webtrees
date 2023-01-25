<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

/**
 * Search for genealogy data
 */
class SearchAdvancedAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $fields      = Validator::parsedBody($request)->array('fields');
        $modifiers   = Validator::parsedBody($request)->array('modifiers');
        $other_field = Validator::parsedBody($request)->string('other_field');
        $other_value = Validator::parsedBody($request)->string('other_value');

        if ($other_field !== '' && $other_value !== '') {
            $fields[$other_field] = $other_value;
        }

        return redirect(route(SearchAdvancedPage::class, [
            'fields'    => $fields,
            'modifiers' => $modifiers,
            'tree'      => $tree->name(),
        ]));
    }
}
