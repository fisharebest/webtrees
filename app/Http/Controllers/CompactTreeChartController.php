<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line ancestors in a compact layout.
 */
class CompactTreeChartController extends AbstractChartController
{
    /**
     * A form to request the chart parameters.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     * @throws \Exception
     */
    public function page(Request $request, Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'compact_tree_chart');

        $xref       = $request->get('xref');
        $individual = Individual::getInstance($xref, $tree);

        $this->checkIndividualAccess($individual);

        /* I18N: %s is an individual’s name */
        $title = I18N::translate('Compact tree of %s', $individual->getFullName());

        return $this->viewResponse('compact-tree-page', [
            'individual' => $individual,
            'title'      => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     * @throws \Exception
     */
    public function chart(Request $request, Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, 'compact_tree_chart');

        $xref       = $request->get('xref');
        $individual = Individual::getInstance($xref, $tree);

        $this->checkIndividualAccess($individual);

        $ancestors = $this->sosaStradonitzAncestors($individual, 5);

        $html = view('compact-tree-chart', [
            'ancestors' => $ancestors,
        ]);

        return new Response($html);
    }
}
