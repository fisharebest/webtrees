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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the repository page.
 */
class RepositoryController extends AbstractBaseController
{
    // Show the repository's facts in this order:
    private const FACT_ORDER = [
        1 => 'NAME',
        'ADDR',
        'NOTE',
        'WWW',
        'REFN',
        'RIN',
        '_UID',
        'CHAN',
        'RESN',
    ];

    /**
     * Show a repository's page.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function show(Request $request, Tree $tree): Response
    {
        $xref   = $request->get('xref', '');
        $record = Repository::getInstance($xref, $tree);

        $this->checkRepositoryAccess($record, false);

        return $this->viewResponse('repository-page', [
            'facts'       => $this->facts($record),
            'meta_robots' => 'index,follow',
            'repository'  => $record,
            'sources'     => $record->linkedSources('REPO'),
            'title'       => $record->getFullName(),
        ]);
    }

    /**
     * @param Repository $record
     *
     * @return array
     */
    private function facts(Repository $record): array
    {
        $facts = $record->facts();

        usort($facts, function (Fact $x, Fact $y): int {
            $sort_x = array_search($x->getTag(), self::FACT_ORDER) ?: PHP_INT_MAX;
            $sort_y = array_search($y->getTag(), self::FACT_ORDER) ?: PHP_INT_MAX;

            return $sort_x <=> $sort_y;
        });

        return $facts;
    }
}
