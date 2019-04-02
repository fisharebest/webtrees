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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class DescendancyChartModule
 */
class DescendancyChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Chart styles
    public const CHART_STYLE_LIST        = 0;
    public const CHART_STYLE_BOOKLET     = 1;
    public const CHART_STYLE_INDIVIDUALS = 2;
    public const CHART_STYLE_FAMILIES    = 3;

    // Defaults
    public const DEFAULT_STYLE               = self::CHART_STYLE_LIST;
    public const DEFAULT_GENERATIONS         = '3';

    // Limits
    public const MINIMUM_GENERATIONS = 2;
    public const MAXIMUM_GENERATIONS = 10;

    /** @var int[] */
    protected $dabo_num = [];

    /** @var string[] */
    protected $dabo_sex = [];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Descendants');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “DescendancyChart” module */
        return I18N::translate('A chart of an individual’s descendants.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-descendants';
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
        return I18N::translate('Descendants of %s', $individual->fullName());
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

        $chart_style = (int) $request->get('chart_style', self::DEFAULT_STYLE);
        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax) {
            return $this->chart($request, $tree, $chart_service);
        }

        $ajax_url = $this->chartUrl($individual, [
            'chart_style' => $chart_style,
            'generations' => $generations,
            'ajax'        => true,
        ]);

        return $this->viewResponse('modules/descendancy_chart/page', [
            'ajax_url'            => $ajax_url,
            'chart_style'         => $chart_style,
            'chart_styles'        => $this->chartStyles(),
            'default_generations' => self::DEFAULT_GENERATIONS,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module_name'         => $this->name(),
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ChartService           $chart_service
     *
     * @return ResponseInterface
     */
    public function chart(ServerRequestInterface $request, Tree $tree, ChartService $chart_service): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        $chart_style = (int) $request->get('chart_style', self::DEFAULT_STYLE);
        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        switch ($chart_style) {
            case self::CHART_STYLE_LIST:
            default:
                return $this->descendantsList($individual, $generations);

            case self::CHART_STYLE_BOOKLET:
                return $this->descendantsBooklet($individual, $generations);

            case self::CHART_STYLE_INDIVIDUALS:
                $individuals = $chart_service->descendants($individual, $generations - 1);

                return $this->descendantsIndividuals($tree, $individuals);

            case self::CHART_STYLE_FAMILIES:
                $families = $chart_service->descendantFamilies($individual, $generations - 1);

                return $this->descendantsFamilies($tree, $families);
        }
    }

    /**
     * Show a hierarchical list of descendants
     *
     * @TODO replace ob_start() with views.
     *
     * @param Individual $individual
     * @param int        $generations
     *
     * @return ResponseInterface
     */
    private function descendantsList(Individual $individual, int $generations): ResponseInterface
    {
        ob_start();

        echo '<ul class="wt-chart-descendants-list list-unstyled">';
        $this->printChildDescendancy($individual, $generations, $generations);
        echo '</ul>';

        $html = ob_get_clean();

        return response($html);
    }

    /**
     * print a child descendancy
     *
     * @param Individual $person
     * @param int        $depth the descendancy depth to show
     * @param int        $generations
     *
     * @return void
     */
    private function printChildDescendancy(Individual $person, $depth, int $generations): void
    {
        echo '<li>';
        echo '<table><tr><td>';
        if ($depth == $generations) {
            echo '<img alt="" role="presentation" src="' . e(asset('css/images/spacer.png')) . '" height="3" width="15"></td><td>';
        } else {
            echo '<img src="' . e(asset('css/images/spacer.png')) . '" height="3" width="3">';
            echo '<img src="' . e(asset('css/images/hline.png')) . '" height="3" width="', 12, '"></td><td>';
        }
        echo FunctionsPrint::printPedigreePerson($person);
        echo '</td>';

        // check if child has parents and add an arrow
        echo '<td></td>';
        echo '<td>';
        foreach ($person->childFamilies() as $cfamily) {
            foreach ($cfamily->spouses() as $parent) {
                echo '<a href="' . e($this->chartUrl($parent, ['generations' => $generations])) . '" title="' .  I18N::translate('Start at parents') . '">' . view('icons/arrow-up') . '<span class="sr-only">' .  I18N::translate('Start at parents') . '</span></a>';
                // only show the arrow for one of the parents
                break;
            }
        }

        // d'Aboville child number
        $level = $generations - $depth;
        echo '<br><br>&nbsp;';
        echo '<span dir="ltr">'; //needed so that RTL languages will display this properly
        if (!isset($this->dabo_num[$level])) {
            $this->dabo_num[$level] = 0;
        }
        $this->dabo_num[$level]++;
        $this->dabo_num[$level + 1] = 0;
        $this->dabo_sex[$level]     = $person->sex();
        for ($i = 0; $i <= $level; $i++) {
            $isf = $this->dabo_sex[$i];
            if ($isf === 'M') {
                $isf = '';
            }
            if ($isf === 'U') {
                $isf = 'NN';
            }
            echo '<span class="person_box' . $isf . '">&nbsp;' . $this->dabo_num[$i] . '&nbsp;</span>';
            if ($i < $level) {
                echo '.';
            }
        }
        echo '</span>';
        echo '</td></tr>';
        echo '</table>';
        echo '</li>';

        // loop for each spouse
        foreach ($person->spouseFamilies() as $family) {
            $this->printFamilyDescendancy($person, $family, $depth, $generations);
        }
    }

    /**
     * print a family descendancy
     *
     * @param Individual $person
     * @param Family     $family
     * @param int        $depth the descendancy depth to show
     * @param int        $generations
     *
     * @return void
     */
    private function printFamilyDescendancy(Individual $person, Family $family, int $depth, int $generations): void
    {
        $uid = Uuid::uuid4()->toString(); // create a unique ID
        // print marriage info
        echo '<li>';
        echo '<img src="', e(asset('css/images/spacer.png')), '" height="2" width="', 19, '">';
        echo '<span class="details1">';
        echo '<a href="#" onclick="expand_layer(\'' . $uid . '\'); return false;" class="top"><i id="' . $uid . '_img" class="icon-minus" title="' . I18N::translate('View this family') . '"></i></a>';
        if ($family->canShow()) {
            foreach ($family->facts(Gedcom::MARRIAGE_EVENTS) as $fact) {
                echo ' <a href="', e($family->url()), '" class="details1">', $fact->summary(), '</a>';
            }
        }
        echo '</span>';

        // print spouse
        $spouse = $family->spouse($person);
        echo '<ul class="generations list-unstyled" id="' . $uid . '">';
        echo '<li>';
        echo '<table><tr><td>';
        echo FunctionsPrint::printPedigreePerson($spouse);
        echo '</td>';

        // check if spouse has parents and add an arrow
        echo '<td></td>';
        echo '<td>';
        if ($spouse) {
            foreach ($spouse->childFamilies() as $cfamily) {
                foreach ($cfamily->spouses() as $parent) {
                    echo '<a href="' . e($this->chartUrl($parent, ['generations' => $generations])) . '" title="' .  strip_tags($this->chartTitle($parent)) . '">' . view('icons/arrow-up') . '<span class="sr-only">' .  strip_tags($this->chartTitle($parent)) . '</span></a>';
                    // only show the arrow for one of the parents
                    break;
                }
            }
        }
        echo '<br><br>&nbsp;';
        echo '</td></tr>';

        // children
        $children = $family->children();
        echo '<tr><td colspan="3" class="details1" >&nbsp;&nbsp;';
        if ($children->isNotEmpty()) {
            echo GedcomTag::getLabel('NCHI') . ': ' . $children->count();
        } else {
            // Distinguish between no children (NCHI 0) and no recorded
            // children (no CHIL records)
            if (strpos($family->gedcom(), '\n1 NCHI 0') !== false) {
                echo GedcomTag::getLabel('NCHI') . ': ' . $children->count();
            } else {
                echo I18N::translate('No children');
            }
        }
        echo '</td></tr></table>';
        echo '</li>';
        if ($depth > 1) {
            foreach ($children as $child) {
                $this->printChildDescendancy($child, $depth - 1, $generations);
            }
        }
        echo '</ul>';
        echo '</li>';
    }

    /**
     * Show a tabular list of individual descendants.
     *
     * @param Tree       $tree
     * @param Collection $individuals
     *
     * @return ResponseInterface
     */
    private function descendantsIndividuals(Tree $tree, Collection $individuals): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/individuals-table', [
            'individuals' => $individuals,
            'sosa'        => false,
            'tree'        => $tree,
        ]);
    }

    /**
     * Show a tabular list of individual descendants.
     *
     * @param Tree       $tree
     * @param Collection $families
     *
     * @return ResponseInterface
     */
    private function descendantsFamilies(Tree $tree, Collection $families): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/families-table', [
            'families' => $families,
            'tree'     => $tree,
        ]);
    }

    /**
     * Show a booklet view of descendants
     *
     * @TODO replace ob_start() with views.
     *
     * @param Individual $individual
     * @param int        $generations
     *
     * @return ResponseInterface
     */
    private function descendantsBooklet(Individual $individual, int $generations): ResponseInterface
    {
        ob_start();

        $this->printChildFamily($individual, $generations);

        $html = ob_get_clean();

        return response($html);
    }

    /**
     * Print a child family
     *
     * @param Individual $individual
     * @param int        $depth     - the descendancy depth to show
     * @param string     $daboville - d'Aboville number
     * @param string     $gpid
     *
     * @return void
     */
    private function printChildFamily(Individual $individual, $depth, $daboville = '1.', $gpid = ''): void
    {
        if ($depth < 2) {
            return;
        }

        $i = 1;

        foreach ($individual->spouseFamilies() as $family) {
            FunctionsCharts::printSosaFamily($family, '', -1, $daboville, $individual->xref(), $gpid, false);
            foreach ($family->children() as $child) {
                $this->printChildFamily($child, $depth - 1, $daboville . ($i++) . '.', $individual->xref());
            }
        }
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @return string[]
     */
    private function chartStyles(): array
    {
        return [
            self::CHART_STYLE_LIST        => I18N::translate('List'),
            self::CHART_STYLE_BOOKLET     => I18N::translate('Booklet'),
            self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
            self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
        ];
    }
}
