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
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    protected const DEFAULT_GENERATIONS         = '3';
    protected const DEFAULT_MAXIMUM_GENERATIONS = '9';

    /**
     * How should this module be labelled on tabs, menus, etc.?
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
        return I18N::translate('Ancestors of %s', $individual->getFullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param Request      $request
     * @param Tree         $tree
     * @param User         $user
     * @param ChartService $chart_service
     *
     * @return Response
     */
    public function getChartAction(Request $request, Tree $tree, User $user, ChartService $chart_service): Response
    {
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $minimum_generations = 2;
        $maximum_generations = (int) $tree->getPreference('MAX_PEDIGREE_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
        $default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

        $show_cousins = (bool) $request->get('show_cousins', self::DEFAULT_COUSINS);
        $chart_style  = $request->get('chart_style', self::DEFAULT_STYLE);
        $generations  = (int) $request->get('generations', $default_generations);

        $generations = min($generations, $maximum_generations);
        $generations = max($generations, $minimum_generations);

        if ($ajax) {
            $ancestors = $chart_service->sosaStradonitzAncestors($individual, $generations);

            switch ($chart_style) {
                default:
                case self::CHART_STYLE_LIST:
                    return $this->ancestorsList($individual, $generations);

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
            'default_generations' => $default_generations,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => $maximum_generations,
            'minimum_generations' => $minimum_generations,
            'module_name'         => $this->name(),
            'show_cousins'        => $show_cousins,
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * Show a hierarchical list of ancestors
     *
     * @TODO replace ob_start() with views.
     *
     * @param Individual $individual
     * @param int        $generations
     *
     * @return Response
     */
    protected function ancestorsList(Individual $individual, int $generations): Response
    {
        ob_start();

        $this->printChildAscendancy($individual, 1, $generations - 1);

        $html = ob_get_clean();

        $html = '<ul class="chart_common">' . $html . '</ul>';

        return new Response($html);
    }

    /**
     * print a child ascendancy
     *
     * @param Individual $individual
     * @param int        $sosa
     * @param int        $generations
     *
     * @return void
     */
    protected function printChildAscendancy(Individual $individual, $sosa, $generations)
    {
        echo '<li class="wt-ancestors-chart-list-item">';
        echo '<table><tbody><tr><td>';
        if ($sosa === 1) {
            echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="15"></td><td>';
        } else {
            echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="3" width="2">';
            echo '<img src="', Theme::theme()->parameter('image-hline'), '" height="3" width="13"></td><td>';
        }
        echo FunctionsPrint::printPedigreePerson($individual);
        echo '</td><td>';
        if ($sosa > 1) {
            echo FontAwesome::linkIcon('arrow-down', $this->chartTitle($individual), [
                'href' => $this->chartUrl($individual, [
                    'generations' => $generations,
                    'chart_style' => self::CHART_STYLE_LIST,
                ])
            ]);
        }
        echo '</td><td class="details1">&nbsp;<span class="person_box' . ($sosa === 1 ? 'NN' : ($sosa % 2 ? 'F' : '')) . '">', I18N::number($sosa), '</span> ';
        echo '</td><td class="details1">&nbsp;', FunctionsCharts::getSosaName($sosa), '</td>';
        echo '</tr></tbody></table>';

        // Parents
        $family = $individual->getPrimaryChildFamily();
        if ($family && $generations > 0) {
            // Marriage details
            echo '<span class="details1">';
            echo '<img src="', Theme::theme()->parameter('image-spacer'), '" height="2" width="15"><a href="#" onclick="return expand_layer(\'sosa_', $sosa, '\');" class="top"><i id="sosa_', $sosa, '_img" class="icon-minus" title="', I18N::translate('View this family'), '"></i></a>';
            echo ' <span class="person_box">', I18N::number($sosa * 2), '</span> ', I18N::translate('and');
            echo ' <span class="person_boxF">', I18N::number($sosa * 2 + 1), '</span>';
            if ($family->canShow()) {
                foreach ($family->facts(Gedcom::MARRIAGE_EVENTS) as $fact) {
                    echo ' <a href="', e($family->url()), '" class="details1">', $fact->summary(), '</a>';
                }
            }
            echo '</span>';
            echo '<ul class="wt-ancestors-chart-list" id="sosa_', $sosa, '">';
            if ($family->getHusband()) {
                $this->printChildAscendancy($family->getHusband(), $sosa * 2, $generations - 1);
            }
            if ($family->getWife()) {
                $this->printChildAscendancy($family->getWife(), $sosa * 2 + 1, $generations - 1);
            }
            echo '</ul>';
        }
        echo '</li>';
    }

    /**
     * Show a tabular list of individual ancestors.
     *
     * @param Tree       $tree
     * @param Collection $ancestors
     *
     * @return Response
     */
    protected function ancestorsIndividuals(Tree $tree, Collection $ancestors): Response
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
     * @return Response
     */
    protected function ancestorsFamilies(Tree $tree, Collection $ancestors): Response
    {
        $this->layout = 'layouts/ajax';

        $families = [];
        foreach ($ancestors as $individual) {
            foreach ($individual->getChildFamilies() as $family) {
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
     * @return Response
     */
    protected function ancestorsBooklet(Collection $ancestors, bool $show_cousins): Response
    {
        ob_start();

        echo FunctionsPrint::printPedigreePerson($ancestors[1]);
        foreach ($ancestors as $sosa => $individual) {
            foreach ($individual->getChildFamilies() as $family) {
                FunctionsCharts::printSosaFamily($family, $individual->xref(), $sosa, '', '', '', $show_cousins);
            }
        }

        $html = ob_get_clean();

        return new Response($html);
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
