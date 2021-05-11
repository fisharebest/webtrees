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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function trim;

/**
 * Add a new fact.
 */
class AddNewFact implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $subtag  = $request->getAttribute('fact');
        $record  = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record  = Auth::checkRecordAccess($record, true);
        $element = Registry::elementFactory()->make($record->tag() . ':' . $subtag);
        $title   = $record->fullName() . ' - ' . $element->label();
        $fact    = new Fact(trim('1 ' . $subtag . ' ' . $element->default($tree)), $record, 'new');

        return $this->viewResponse('edit/edit-fact', [
            'can_edit_raw' => false,
            'fact'         => $fact,
            'title'        => $title,
            'tree'         => $tree,
            'url'          => $record->url(),
        ]);
    }
}
