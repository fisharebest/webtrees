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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AncestorsChartModule
 */
class AncestorsChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Chart styles
    protected const CHART_STYLE_LIST        = 'list';
    protected const CHART_STYLE_BOOKLET     = 'booklet';
    protected const CHART_STYLE_INDIVIDUALS = 'individuals';
    protected const CHART_STYLE_FAMILIES    = 'families';

    // Defaults
    protected const DEFAULT_COUSINS             = false;
    protected const DEFAULT_STYLE               = self::CHART_STYLE_LIST;
    protected const DEFAULT_GENERATIONS         = '4';

    // Limits
    protected const MINIMUM_GENERATIONS = 2;
    protected const MAXIMUM_GENERATIONS = 10;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Ancestors');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “AncestorsChart” module */
        return I18N::translate('A chart of an individual’s ancestors.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-ancestry';
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
        return I18N::translate('Ancestors of %s', $individual->fullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     * @param ChartService           $chart_service
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user, ChartService $chart_service): ResponseInterface
    {
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $show_cousins = (bool) $request->get('show_cousins', self::DEFAULT_COUSINS);
        $chart_style  = $request->get('chart_style', self::DEFAULT_STYLE);
        $generations  = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax) {
            $ancestors = $chart_service->sosaStradonitzAncestors($individual, $generations);

            switch ($chart_style) {
                default:
                case self::CHART_STYLE_LIST:
                    return response(view('modules/ancestors-chart/list', ['individual' => $individual, 'parents' => $individual->primaryChildFamily(), 'generations' => $generations, 'sosa' => 1]));

                case self::CHART_STYLE_BOOKLET:
                    return $this->ancestorsBooklet($ancestors, $show_cousins);

                case self::CHART_STYLE_INDIVIDUALS:
                    return $this->ancestorsIndividuals($tree, $ancestors);

                case self::CHART_STYLE_FAMILIES:
                    return $this->ancestorsFamilies($tree, $ancestors);
            }
        }

        $ajax_url = $this->chartUrl($individual, [
            'generations'  => $generations,
            'chart_style'  => $chart_style,
            'show_cousins' => $show_cousins,
            'ajax'         => true,
        ]);

        return $this->viewResponse('modules/ancestors-chart/page', [
            'ajax_url'            => $ajax_url,
            'chart_style'         => $chart_style,
            'chart_styles'        => $this->chartStyles(),
            'default_generations' => self::DEFAULT_GENERATIONS,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module_name'         => $this->name(),
            'show_cousins'        => $show_cousins,
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * Show a tabular list of individual ancestors.
     *
     * @param Tree       $tree
     * @param Collection $ancestors
     *
     * @return ResponseInterface
     */
    protected function ancestorsIndividuals(Tree $tree, Collection $ancestors): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/individuals-table', [
            'individuals' => $ancestors,
            'sosa'        => true,
            'tree'        => $tree,
        ]);
    }

    /**
     * Show a tabular list of individual ancestors.
     *
     * @param Tree       $tree
     * @param Collection $ancestors
     *
     * @return ResponseInterface
     */
    protected function ancestorsFamilies(Tree $tree, Collection $ancestors): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $families = [];
        foreach ($ancestors as $individual) {
            foreach ($individual->childFamilies() as $family) {
                $families[$family->xref()] = $family;
            }
        }

        return $this->viewResponse('lists/families-table', [
            'families' => $families,
            'tree'     => $tree,
        ]);
    }

    /**
     * Show a booklet view of ancestors
     *
     * @TODO replace ob_start() with views.
     *
     * @param Collection $ancestors
     * @param bool       $show_cousins
     *
     * @return ResponseInterface
     */
    protected function ancestorsBooklet(Collection $ancestors, bool $show_cousins): ResponseInterface
    {
        ob_start();

        echo FunctionsPrint::printPedigreePerson($ancestors[1]);
        foreach ($ancestors as $sosa => $individual) {
            foreach ($individual->childFamilies() as $family) {
                FunctionsCharts::printSosaFamily($family, $individual->xref(), $sosa, '', '', '', $show_cousins);
            }
        }

        $html = ob_get_clean();

        return response($html);
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @return array
     */
    protected function chartStyles(): array
    {
        return [
            self::CHART_STYLE_LIST        => I18N::translate('List'),
            self::CHART_STYLE_BOOKLET     => I18N::translate('Booklet'),
            self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
            self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
        ];
    }
}
