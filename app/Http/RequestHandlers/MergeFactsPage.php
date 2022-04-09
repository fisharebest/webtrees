<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Merge records
 */
class MergeFactsPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * Merge two genealogy records.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree = Validator::attributes($request)->tree();

        $xref1 = $request->getQueryParams()['xref1'] ?? '';
        $xref2 = $request->getQueryParams()['xref2'] ?? '';

        $title = I18N::translate('Merge records') . ' — ' . e($tree->title());

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
            return redirect(route(MergeRecordsPage::class, [
                'tree'  => $tree->name(),
                'xref1' => $xref1,
                'xref2' => $xref2,
            ]));
        }

        // Facts found both records
        $facts = [];

        // Facts found in only one record
        $facts1 = [];
        $facts2 = [];

        foreach ($record1->facts() as $fact) {
            if (!$fact->isPendingDeletion() && !str_ends_with($fact->tag(), ':CHAN')) {
                $facts1[$fact->id()] = $fact;
            }
        }

        foreach ($record2->facts() as $fact) {
            if (!$fact->isPendingDeletion() && !str_ends_with($fact->tag(), ':CHAN')) {
                $facts2[$fact->id()] = $fact;
            }
        }

        foreach ($facts1 as $id1 => $fact1) {
            foreach ($facts2 as $id2 => $fact2) {
                if ($fact1->id() === $fact2->id()) {
                    $facts[] = $fact1;
                    unset($facts1[$id1], $facts2[$id2]);
                }
            }
        }

        return $this->viewResponse('admin/merge-records-step-2', [
            'facts'   => $facts,
            'facts1'  => $facts1,
            'facts2'  => $facts2,
            'record1' => $record1,
            'record2' => $record2,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }
}
