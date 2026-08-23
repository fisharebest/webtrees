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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;
use function route;

final class MergeRecords
{
    use ViewResponseTrait;

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $xref1 = Validator::queryParams($request)->isXref()->string('xref1', '');
        $xref2 = Validator::queryParams($request)->isXref()->string('xref2', '');

        $record1 = Registry::gedcomRecordFactory()->make($xref1, $tree);
        $record2 = Registry::gedcomRecordFactory()->make($xref2, $tree);

        $title = I18N::translate('Merge records') . ' — ' . e($tree->title());

        return $this->viewResponse('admin/merge-records-step-1', [
            'individual1' => $record1 instanceof Individual ? $record1 : null,
            'individual2' => $record2 instanceof Individual ? $record2 : null,
            'family1'     => $record1 instanceof Family ? $record1 : null,
            'family2'     => $record2 instanceof Family ? $record2 : null,
            'source1'     => $record1 instanceof Source ? $record1 : null,
            'source2'     => $record2 instanceof Source ? $record2 : null,
            'repository1' => $record1 instanceof Repository ? $record1 : null,
            'repository2' => $record2 instanceof Repository ? $record2 : null,
            'media1'      => $record1 instanceof Media ? $record1 : null,
            'media2'      => $record2 instanceof Media ? $record2 : null,
            'note1'       => $record1 instanceof Note ? $record1 : null,
            'note2'       => $record2 instanceof Note ? $record2 : null,
            'submitter1'  => $record1 instanceof Submitter ? $record1 : null,
            'submitter2'  => $record2 instanceof Submitter ? $record2 : null,
            'location1'   => $record1 instanceof Location ? $record1 : null,
            'location2'   => $record2 instanceof Location ? $record2 : null,
            'title'       => $title,
            'tree'        => $tree,
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref1 = Validator::parsedBody($request)->isXref()->string('xref1');
        $xref2 = Validator::parsedBody($request)->isXref()->string('xref2');

        // Merge record2 into record1
        $record1 = Registry::gedcomRecordFactory()->make($xref1, $tree);
        $record2 = Registry::gedcomRecordFactory()->make($xref2, $tree);

        if (
            $record1 === null ||
            $record2 === null ||
            $record1 === $record2 ||
            $record1->tag() !== $record2->tag() ||
            $record1->isPendingDeletion() ||
            $record2->isPendingDeletion()
        ) {
            return redirect(route(self::class, [
                'tree'  => $tree->name(),
                'xref1' => $xref1,
                'xref2' => $xref2,
            ]));
        }

        return redirect(route(MergeFacts::class, [
            'tree'  => $tree->name(),
            'xref1' => $xref1,
            'xref2' => $xref2,
        ]));
    }
}
