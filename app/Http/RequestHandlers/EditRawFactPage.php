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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function redirect;

/**
 * Edit the raw GEDCOM of a fact.
 */
class EditRawFactPage implements RequestHandlerInterface
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

        $xref   = $request->getAttribute('xref');
        $record = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        $fact_id = $request->getAttribute('fact_id');

        $title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->fullName();

        foreach ($record->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->id() === $fact_id) {
                return $this->viewResponse('edit/raw-gedcom-fact', [
                    'fact'    => $fact,
                    'title'   => $title,
                    'tree'    => $tree,
                ]);
            }
        }

        return redirect($record->url());
    }
}
