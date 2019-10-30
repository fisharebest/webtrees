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
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function assert;

/**
 * Edit a fact.
 */
class EditFact implements RequestHandlerInterface
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

        $xref    = $request->getQueryParams()['xref'];
        $fact_id = $request->getQueryParams()['fact_id'];

        $record  = GedcomRecord::getInstance($xref, $tree);
        $record  = Auth::checkRecordAccess($record, true);

        // Find the fact to edit
        $edit_fact = null;
        foreach ($record->facts() as $fact) {
            if ($fact->id() === $fact_id && $fact->canEdit()) {
                $edit_fact = $fact;
                break;
            }
        }
        if ($edit_fact === null) {
            throw new NotFoundHttpException();
        }

        $can_edit_raw = Auth::isAdmin() || $tree->getPreference('SHOW_GEDCOM_RECORD');

        $title = $record->fullName() . ' - ' . GedcomTag::getLabel($edit_fact->getTag());

        return $this->viewResponse('edit/edit-fact', [
            'can_edit_raw' => $can_edit_raw,
            'edit_fact'    => $edit_fact,
            'record'       => $record,
            'title'        => $title,
            'tree'         => $tree,
        ]);
    }
}
