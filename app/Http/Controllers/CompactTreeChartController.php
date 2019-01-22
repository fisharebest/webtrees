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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line ancestors in a compact layout.
 */
class CompactTreeChartController extends AbstractBaseController
{
    /**
     * A form to request the chart parameters.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function page(Request $request, Tree $tree): Response
    {
        $this->checkModuleIsActive($tree, CompactTreeChartModule::class);

        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        /* I18N: %s is an individual’s name */
        $title = I18N::translate('Compact tree of %s', $individual->getFullName());

        return $this->viewResponse('compact-tree-page', [
            'individual' => $individual,
            'title'      => $title,
        ]);
    }

    /**
     * @param Request      $request
     * @param Tree         $tree
     * @param ChartService $chart_service
     *
     * @return Response
     */
    public function chart(Request $request, Tree $tree, ChartService $chart_service): Response
    {
        $this->checkModuleIsActive($tree, CompactTreeChartModule::class);

        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        $ancestors = $chart_service->sosaStradonitzAncestors($individual, 5);

        $html = view('compact-tree-chart', [
            'ancestors' => $ancestors,
        ]);

        return new Response($html);
    }
}
