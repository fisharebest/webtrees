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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CompactTreeChartModule
 */
class CompactTreeChartModule extends AbstractModule implements ModuleInterface, ModuleChartInterface
{
    use ModuleChartTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Compact tree');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “CompactTreeChart” module */
        return I18N::translate('A chart of an individual’s ancestors, as a compact tree.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-compact';
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        /* I18N: %s is an individual’s name */
        return I18N::translate('Compact tree of %s', $individual->getFullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param Request      $request
     * @param Tree         $tree
     * @param ChartService $chart_service
     *
     * @return Response
     */
    public function getChartAction(Request $request, Tree $tree, ChartService $chart_service): Response
    {
        $ajax       = $request->get('ajax', '');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        /* I18N: %s is an individual’s name */
        $title = I18N::translate('Compact tree of %s', $individual->getFullName());

        if ($ajax === '1') {
            return $this->chartCompact($individual, $chart_service);
        }

        return $this->viewResponse('modules/compact-chart/chart-page', [
            'individual' => $individual,
            'module'     => $this,
            'title'      => $title,
        ]);
    }

    /**
     * @param Individual   $individual
     * @param ChartService $chart_service
     *
     * @return Response
     */
    protected function chartCompact(Individual $individual, ChartService $chart_service): Response
    {
        $ancestors = $chart_service->sosaStradonitzAncestors($individual, 5);

        $html = view('modules/compact-chart/chart', [
            'ancestors' => $ancestors,
        ]);

        return new Response($html);
    }
}
