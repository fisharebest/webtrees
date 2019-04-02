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
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class HourglassChartModule
 */
class HourglassChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    private const DEFAULT_GENERATIONS         = '3';
    private const DEFAULT_MAXIMUM_GENERATIONS = '9';

    // Limits
    private const MAXIMUM_GENERATIONS = 10;
    private const MINIMUM_GENERATIONS = 2;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Hourglass chart');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “HourglassChart” module */
        return I18N::translate('An hourglass chart of an individual’s ancestors and descendants.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-hourglass';
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
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        $show_spouse = (bool) $request->get('show_spouse');

        if ($ajax) {
            return $this->chart($individual, $generations, $show_spouse);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'show_spouse' => $show_spouse,
        ]);

        return $this->viewResponse('modules/hourglass-chart/page', [
            'ajax_url'            => $ajax_url,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module_name'         => $this->name(),
            'show_spouse'         => $show_spouse,
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * Generate the initial generations of the chart
     *
     * @param Individual $individual
     * @param int        $generations
     * @param bool       $show_spouse
     *
     * @return ResponseInterface
     */
    protected function chart(Individual $individual, int $generations, bool $show_spouse): ResponseInterface
    {
        ob_start();
        $this->printDescendency($individual, 1, $generations, $show_spouse, true);
        $descendants = ob_get_clean();

        ob_start();
        $this->printPersonPedigree($individual, 1, $generations, $show_spouse);
        $ancestors = ob_get_clean();

        return response(view('modules/hourglass-chart/chart', [
            'descendants' => $descendants,
            'ancestors'   => $ancestors,
            'bhalfheight' => (int) (app(ModuleThemeInterface::class)->parameter('chart-box-y') / 2),
            'module_name' => $this->name(),
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postAncestorsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        $show_spouse = (bool) $request->get('show_spouse');

        ob_start();
        $this->printPersonPedigree($individual, 0, 1, $show_spouse);
        $html = ob_get_clean();

        return response($html);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postDescendantsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $show_spouse = (bool) $request->get('show_spouse');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new NotFoundHttpException();
        }

        ob_start();
        $this->printDescendency($individual, 1, 2, $show_spouse, false);
        $html = ob_get_clean();

        return response($html);
    }

    /**
     * Prints descendency of passed in person
     *
     * @param Individual $individual  Show descendants of this individual
     * @param int        $generation  The current generation number
     * @param int        $generations Show this number of generations
     * @param bool       $show_spouse
     * @param bool       $show_menu
     *
     * @return void
     */
    private function printDescendency(Individual $individual, int $generation, int $generations, bool $show_spouse, bool $show_menu): void
    {
        static $lastGenSecondFam = false;

        if ($generation > $generations) {
            return;
        }
        $pid         = $individual->xref();
        $tablealign  = 'right';
        $otablealign = 'left';
        if (I18N::direction() === 'rtl') {
            $tablealign  = 'left';
            $otablealign = 'right';
        }

        //-- put a space between families on the last generation
        if ($generation == $generations - 1) {
            if ($lastGenSecondFam) {
                echo '<br>';
            }
            $lastGenSecondFam = true;
        }
        echo '<table cellspacing="0" cellpadding="0" border="0" id="table_' . e($pid) . '" class="hourglassChart" style="float:' . $tablealign . '">';
        echo '<tr>';
        echo '<td style="text-align:' . $tablealign . '">';
        $families = $individual->spouseFamilies();
        $children = [];
        if ($generation < $generations) {
            // Put all of the children in a common array
            foreach ($families as $family) {
                foreach ($family->children() as $child) {
                    $children[] = $child;
                }
            }

            $ct = count($children);
            if ($ct > 0) {
                echo '<table cellspacing="0" cellpadding="0" border="0" style="position: relative; top: auto; float: ' . $tablealign . ';">';
                for ($i = 0; $i < $ct; $i++) {
                    $individual2 = $children[$i];
                    $chil        = $individual2->xref();
                    echo '<tr>';
                    echo '<td id="td_', e($chil), '" class="', I18N::direction(), '" style="text-align:', $otablealign, '">';
                    $this->printDescendency($individual2, $generation + 1, $generations, $show_spouse, false);
                    echo '</td>';

                    // Print the lines
                    if ($ct > 1) {
                        if ($i == 0) {
                            // First child
                            echo '<td style="vertical-align:bottom"><img alt="" role="presentation" class="line1 tvertline" id="vline_' . $chil . '" src="' . e(asset('css/images/vline.png')) . '" width="3"></td>';
                        } elseif ($i == $ct - 1) {
                            // Last child
                            echo '<td style="vertical-align:top"><img alt="" role="presentation" class="bvertline" id="vline_' . $chil . '" src="' . e(asset('css/images/vline.png')) . '" width="3"></td>';
                        } else {
                            // Middle child
                            echo '<td style="background:url(' . e('"' . asset('css/images/vline.png') . '"') . ');"><img alt="" role="presentation" src="' . e(asset('css/images/spacer.png')) . '" width="3"></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }
            echo '</td>';
            echo '<td class="myCharts" width="', app(ModuleThemeInterface::class)->parameter('chart-box-x'), '">';
        }

        // Print the descendency expansion arrow
        if ($generation === $generations) {
            $tbwidth = app(ModuleThemeInterface::class)->parameter('chart-box-x') + 16;
            for ($j = $generation; $j < $generations; $j++) {
                echo "<div style='width: ", $tbwidth, "px;'><br></div></td><td style='width:", app(ModuleThemeInterface::class)->parameter('chart-box-x'), "px'>";
            }
            $kcount = 0;
            foreach ($families as $family) {
                $kcount += $family->numberOfChildren();
            }
            if ($kcount == 0) {
                echo "</td><td style='width:", app(ModuleThemeInterface::class)->parameter('chart-box-x'), "px'>";
            } else {
                echo '<a href="#" title="' . I18N::translate('Children') . '" data-route="Descendants" data-xref="' .  e($pid) . '" data-spouses="' .  e($show_spouse) . '" data-tree="' .  e($individual->tree()->name()) . '">' . view('icons/arrow-left') . '</a>';

                //-- move the arrow up to line up with the correct box
                if ($show_spouse) {
                    echo str_repeat('<br><br><br>', count($families));
                }
                echo "</td><td style='width:", app(ModuleThemeInterface::class)->parameter('chart-box-x'), "px'>";
            }
        }

        echo '<table cellspacing="0" cellpadding="0" border="0" id="table2_' . $pid . '"><tr><td> ';
        echo FunctionsPrint::printPedigreePerson($individual);
        echo '</td><td> <img alt="" role="presentation" class="lineh1" src="' . e(asset('css/images/hline.png')) . '" width="7" height="3">';

        //----- Print the spouse
        if ($show_spouse) {
            foreach ($families as $family) {
                echo "</td></tr><tr><td style='text-align:$otablealign'>";
                echo FunctionsPrint::printPedigreePerson($family->spouse($individual));
                echo '</td><td> </td>';
            }
            //-- add offset divs to make things line up better
            if ($generation == $generations) {
                echo "<tr><td colspan '2'><div style='height:", (app(ModuleThemeInterface::class)->parameter('chart-box-y') / 4), 'px; width:', app(ModuleThemeInterface::class)->parameter('chart-box-x'), "px;'><br></div>";
            }
        }
        echo '</td></tr></table>';

        // For the root individual, print a down arrow that allows changing the root of tree
        if ($show_menu && $generation == 1) {
            echo '<div class="text-center" id="childarrow" style="position:absolute; width:', app(ModuleThemeInterface::class)->parameter('chart-box-x'), 'px;">';
            echo '<a href="#" title="' . I18N::translate('Family') . '" id="spouse-child-links">' . view('icons/arrow-down') . '</a>';
            echo '<div id="childbox">';
            echo '<table cellspacing="0" cellpadding="0" border="0" class="person_box"><tr><td> ';

            foreach ($individual->spouseFamilies() as $family) {
                echo "<span class='name1'>" . I18N::translate('Family') . '</span>';
                $spouse = $family->spouse($individual);
                if ($spouse !== null) {
                    echo '<a href="' . e(route('hourglass', [
                            'xref'        => $spouse->xref(),
                            'generations' => $generations,
                            'show_spouse' => (int) $show_spouse,
                            'ged'         => $spouse->tree()->name(),
                        ])) . '" class="name1">' . $spouse->fullName() . '</a>';
                }
                foreach ($family->children() as $child) {
                    echo '<a href="' . e(route('hourglass', [
                            'xref'        => $child->xref(),
                            'generations' => $generations,
                            'show_spouse' => (int) $show_spouse,
                            'ged'         => $child->tree()->name(),
                        ])) . '" class="name1">' . $child->fullName() . '</a>';
                }
            }

            //-- print the siblings
            foreach ($individual->childFamilies() as $family) {
                if ($family->husband() || $family->wife()) {
                    echo "<span class='name1'>" . I18N::translate('Parents') . '</span>';
                    $husb = $family->husband();
                    if ($husb) {
                        echo '<a href="' . e(route('hourglass', [
                                'xref'        => $husb->xref(),
                                'generations' => $generations,
                                'show_spouse' => (int) $show_spouse,
                                'ged'         => $husb->tree()->name(),
                            ])) . '" class="name1">' . $husb->fullName() . '</a>';
                    }
                    $wife = $family->wife();
                    if ($wife) {
                        echo '<a href="' . e(route('hourglass', [
                                'xref'        => $wife->xref(),
                                'generations' => $generations,
                                'show_spouse' => (int) $show_spouse,
                                'ged'         => $wife->tree()->name(),
                            ])) . '" class="name1">' . $wife->fullName() . '</a>';
                    }
                }

                // filter out root person from children array so only siblings remain
                $siblings       = $family->children()->filter(static function (Individual $x) use ($individual): bool {
                    return $x !== $individual;
                });

                if ($siblings->count() > 0) {
                    echo '<span class="name1">';
                    echo $siblings->count() > 1 ? I18N::translate('Siblings') : I18N::translate('Sibling');
                    echo '</span>';
                    foreach ($siblings as $child) {
                        echo '<a href="' . e(route('hourglass', [
                                'xref'        => $child->xref(),
                                'generations' => $generations,
                                'show_spouse' => (int) $show_spouse,
                                'ged'         => $child->tree()->name(),
                            ])) . '" class="name1">' . $child->fullName() . '</a>';
                    }
                }
            }
            echo '</td></tr></table>';
            echo '</div>';
            echo '</div>';
        }
        echo '</td></tr></table>';
    }

    /**
     * Prints pedigree of the person passed in. Which is the descendancy
     *
     * @param Individual $individual  Show the pedigree of this individual
     * @param int        $generation  Current generation number
     * @param int        $generations Show this number of generations
     * @param bool       $show_spouse
     *
     * @return void
     */
    private function printPersonPedigree(Individual $individual, int $generation, int $generations, bool $show_spouse): void
    {
        if ($generation >= $generations) {
            return;
        }

        // handle pedigree n generations lines
        $genoffset = $generations;

        $family = $individual->primaryChildFamily();

        if ($family === null) {
            // Prints empty table columns for children w/o parents up to the max generation
            // This allows vertical line spacing to be consistent
            echo '<table><tr><td><div class="wt-chart-box"></div></td>';
            echo '<td> ';
            // Recursively get the father’s family
            $this->printPersonPedigree($individual, $generation + 1, $generations, $show_spouse);
            echo '</td></tr>';
            echo '<tr><td><div class="wt-chart-box"></div></td>';
            echo '<td> ';
            // Recursively get the mother’s family
            $this->printPersonPedigree($individual, $generation + 1, $generations, $show_spouse);
            echo '</td><td> </tr></table>';
        } else {
            echo '<table cellspacing="0" cellpadding="0" border="0"  class="hourglassChart">';
            echo '<tr>';
            echo '<td style="vertical-align:bottom"><img alt="" role="presnentation" class="line3 pvline" src="' . e(asset('css/images/vline.png')) . '" width="3"></td>';
            echo '<td> <img alt="" role="presentation" class="lineh2" src="' . e(asset('css/images/hline.png')) . '" width="7" height="3"></td>';
            echo '<td class="myCharts"> ';
            //-- print the father box
            echo FunctionsPrint::printPedigreePerson($family->husband());
            echo '</td>';
            if ($family->husband()) {
                $ARID = $family->husband()->xref();
                echo '<td id="td_' . e($ARID) . '">';

                if ($generation == $generations - 1 && $family->husband()->childFamilies()) {
                    echo '<a href="#" title="' . I18N::translate('Parents') . '" data-route="Ancestors" data-xref="' .  e($ARID) . '" data-spouses="' .  e($show_spouse) . '" data-tree="' .  e($family->husband()->tree()->name()) . '">' . view('icons/arrow-right') . '</a>';
                }

                $this->printPersonPedigree($family->husband(), $generation + 1, $generations, $show_spouse);
                echo '</td>';
            } else {
                echo '<td> ';
                if ($generation < $genoffset - 1) {
                    echo '<table>';
                    for ($i = $generation; $i < ((2 ** (($genoffset - 1) - $generation)) / 2) + 2; $i++) {
                        echo '<div class="wt-chart-box"></div>';
                        echo '</tr>';
                        echo '<div class="wt-chart-box"></div>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            echo
            '</tr><tr>',
                '<td style="vertical-align:top"><img alt="" role="presentation" class="pvline" src="' . e(asset('css/images/vline.png')) . '" width="3"></td>',
                '<td> <img alt="" role="presentation" class="lineh3" src="' . e(asset('css/images/hline.png')) . '" width="7" height="3"></td>',
            '<td class="myCharts"> ';

            echo FunctionsPrint::printPedigreePerson($family->wife());
            echo '</td>';
            if ($family->wife()) {
                $ARID = $family->wife()->xref();
                echo '<td id="td_' . e($ARID) . '">';

                if ($generation == $generations - 1 && $family->wife()->childFamilies()) {
                    echo '<a href="#" title="' . I18N::translate('Parents') . '" data-route="Ancestors" data-xref="' .  e($ARID) . '" data-spouses="' .  e($show_spouse) . '" data-tree="' .  e($family->wife()->tree()->name()) . '">' . view('icons/arrow-right') . '</a>';
                }

                $this->printPersonPedigree($family->wife(), $generation + 1, $generations, $show_spouse);
                echo '</td>';
            }
            echo '</tr></table>';
        }
    }
}
