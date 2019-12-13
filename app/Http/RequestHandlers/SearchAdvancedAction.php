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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Search for genealogy data
 */
class SearchAdvancedAction implements RequestHandlerInterface
{
    /**
     * The standard search.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();

        $fields      = $params['fields'] ?? [];
        $modifiers   = $params['modifiers'] ?? [];
        $other_field = $params['other_field'] ?? '';
        $other_value = $params['other_value'] ?? '';

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
