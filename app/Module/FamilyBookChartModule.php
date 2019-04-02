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
use stdClass;

/**
 * Class FamilyBookChartModule
 */
class FamilyBookChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    private const DEFAULT_GENERATIONS            = '2';
    private const DEFAULT_DESCENDANT_GENERATIONS = '5';
    private const DEFAULT_MAXIMUM_GENERATIONS    = '9';

    // Limits
    public const MINIMUM_GENERATIONS = 2;
    public const MAXIMUM_GENERATIONS = 10;

    /** @var stdClass */
    private $box;

    /** @var bool */
    private $show_spouse;

    /** @var int */
    private $descent;

    /** @var int */
    private $bhalfheight;

    /** @var int */
    private $generations;

    /** @var int */
    private $dgenerations;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Family book');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “FamilyBookChart” module */
        return I18N::translate('A chart of an individual’s ancestors and descendants, as a family book.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-familybook';
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
        return I18N::translate('Family book of %s', $individual->fullName());
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

        $show_spouse = (bool) $request->get('show_spouse');
        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);
        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        // Generations of ancestors/descendants in each mini-tree.
        $book_size = (int) $request->get('book_size', 2);
        $book_size = min($book_size, 5);
        $book_size = max($book_size, 2);

        if ($ajax) {
            return $this->chart($individual, $generations, $book_size, $show_spouse);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'book_size'   => $book_size,
            'generations' => $generations,
            'show_spouse' => $show_spouse,
        ]);

        return $this->viewResponse('modules/family-book-chart/page', [
            'ajax_url'            => $ajax_url,
            'book_size'           => $book_size,
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
     * @param Individual $individual
     * @param int        $generations
     * @param int        $book_size
     * @param bool       $show_spouse
     *
     * @return ResponseInterface
     */
    public function chart(Individual $individual, int $generations, int $book_size, bool $show_spouse): ResponseInterface
    {
        $this->box = (object) [
            'width'  => app(ModuleThemeInterface::class)->parameter('chart-box-x'),
            'height' => app(ModuleThemeInterface::class)->parameter('chart-box-y'),
        ];

        $this->show_spouse = $show_spouse;
        $this->descent     = $generations;
        $this->generations = $book_size;

        $this->bhalfheight  = $this->box->height / 2;
        $this->dgenerations = $this->maxDescendencyGenerations($individual, 0);

        if ($this->dgenerations < 1) {
            $this->dgenerations = 1;
        }

        // @TODO - this is just a wrapper around the old code.
        ob_start();
        $this->printFamilyBook($individual, $generations);
        $html = ob_get_clean();

        return response($html);
    }

    /**
     * Prints descendency of passed in person
     *
     * @param int             $generation
     * @param Individual|null $person
     *
     * @return float
     */
    private function printDescendency($generation, Individual $person = null): float
    {
        if ($generation > $this->dgenerations) {
            return 0;
        }

        echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
        $numkids = 0.0;

        // Load children
        $children = [];
        if ($person instanceof Individual) {
            // Count is position from center to left, dgenerations is number of generations
            if ($generation < $this->dgenerations) {
                // All children, from all partners
                foreach ($person->spouseFamilies() as $family) {
                    foreach ($family->children() as $child) {
                        $children[] = $child;
                    }
                }
            }
        }
        if ($generation < $this->dgenerations) {
            if (!empty($children)) {
                // real people
                echo '<table cellspacing="0" cellpadding="0" border="0" >';
                foreach ($children as $i => $child) {
                    echo '<tr><td>';
                    $kids    = $this->printDescendency($generation + 1, $child);
                    $numkids += $kids;
                    echo '</td>';
                    // Print the lines
                    if (count($children) > 1) {
                        if ($i === 0) {
                            // Adjust for the first column on left
                            $h = round((($this->box->height * $kids) + 8) / 2); // Assumes border = 1 and padding = 3
                            //  Adjust for other vertical columns
                            if ($kids > 1) {
                                $h = ($kids - 1) * 4 + $h;
                            }
                            echo '<td class="align-bottom">',
                            '<img id="vline_', $child->xref(), '" src="', e(asset('css/images/vline.png')), '" width="3" height="', $h - 4, '"></td>';
                        } elseif ($i === count($children) - 1) {
                            // Adjust for the first column on left
                            $h = round((($this->box->height * $kids) + 8) / 2);
                            // Adjust for other vertical columns
                            if ($kids > 1) {
                                $h = ($kids - 1) * 4 + $h;
                            }
                            echo '<td class="align-top">',
                            '<img class="bvertline" width="3" id="vline_', $child->xref(), '" src="', e(asset('css/images/vline.png')), '" height="', $h - 2, '"></td>';
                        } else {
                            echo '<td class="align-bottomm"style="background: url(', e(asset('css/images/vline.png')), ');">',
                            '<img class="spacer"  width="3" src="', e(asset('css/images/spacer.png')), '"></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                // Hidden/empty boxes - to preserve the layout
                echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
                $numkids += $this->printDescendency($generation + 1, null);
                echo '</td></tr></table>';
            }
            echo '</td>';
            echo '<td>';
        }

        if ($numkids === 0.0) {
            $numkids = 1;
        }
        echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
        if ($person instanceof Individual) {
            echo FunctionsPrint::printPedigreePerson($person);
            echo '</td><td>',
            '<img class="linef1" src="', e(asset('css/images/hline.png')), '" width="8" height="3">';
        } else {
            echo '<div style="width:', $this->box->width + 19, 'px; height:', $this->box->height + 8, 'px;"></div>',
            '</td><td>';
        }

        // Print the spouse
        if ($generation === 1 && $person instanceof Individual) {
            if ($this->show_spouse) {
                foreach ($person->spouseFamilies() as $family) {
                    $spouse = $family->spouse($person);
                    echo '</td></tr><tr><td>';
                    echo FunctionsPrint::printPedigreePerson($spouse);
                    $numkids += 0.95;
                    echo '</td><td>';
                }
            }
        }
        echo '</td></tr></table>';
        echo '</td></tr>';
        echo '</table>';

        return $numkids;
    }

    /**
     * Prints pedigree of the person passed in
     *
     * @param Individual $person
     * @param int        $count
     *
     * @return void
     */
    private function printPersonPedigree($person, $count): void
    {
        if ($count >= $this->generations) {
            return;
        }

        $genoffset = $this->generations; // handle pedigree n generations lines
        //-- calculate how tall the lines should be
        $lh = $this->bhalfheight * (2 ** ($genoffset - $count - 1));
        //
        //Prints empty table columns for children w/o parents up to the max generation
        //This allows vertical line spacing to be consistent
        if ($person->childFamilies()->isEmpty()) {
            echo '<table cellspacing="0" cellpadding="0" border="0" >';
            echo '<div class="wt-chart-box"></div>';

            //-- recursively get the father’s family
            $this->printPersonPedigree($person, $count + 1);
            echo '</td><td></tr>';
            echo '<div class="wt-chart-box"></div>';

            //-- recursively get the mother’s family
            $this->printPersonPedigree($person, $count + 1);
            echo '</td><td></tr></table>';
        }

        // Empty box section done, now for regular pedigree
        foreach ($person->childFamilies() as $family) {
            echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td class="align-bottom">';
            // Determine line height for two or more spouces
            // And then adjust the vertical line for the root person only
            $famcount = 0;
            if ($this->show_spouse) {
                // count number of spouses
                $famcount += $person->spouseFamilies()->count();
            }
            $savlh = $lh; // Save current line height
            if ($count == 1 && $genoffset <= $famcount) {
                $linefactor = 0;
                // genoffset of 2 needs no adjustment
                if ($genoffset > 2) {
                    $tblheight = $this->box->height + 8;
                    if ($genoffset == 3) {
                        if ($famcount == 3) {
                            $linefactor = $tblheight / 2;
                        } elseif ($famcount > 3) {
                            $linefactor = $tblheight;
                        }
                    }
                    if ($genoffset == 4) {
                        if ($famcount == 4) {
                            $linefactor = $tblheight;
                        } elseif ($famcount > 4) {
                            $linefactor = ($famcount - $genoffset) * ($tblheight * 1.5);
                        }
                    }
                    if ($genoffset == 5) {
                        if ($famcount == 5) {
                            $linefactor = 0;
                        } elseif ($famcount > 5) {
                            $linefactor = $tblheight * ($famcount - $genoffset);
                        }
                    }
                }
                $lh = (($famcount - 1) * $this->box->height - $linefactor);
                if ($genoffset > 5) {
                    $lh = $savlh;
                }
            }
            echo '<img class="line3 pvline"  src="', e(asset('css/images/vline.png')), '" width="3" height="', $lh, '"></td>',
            '<td>',
            '<img class="linef2" src="', e(asset('css/images/hline.png')), '" height="3"></td>',
            '<td>';
            $lh = $savlh; // restore original line height
            //-- print the father box
            echo FunctionsPrint::printPedigreePerson($family->husband());
            echo '</td>';
            if ($family->husband()) {
                echo '<td>';
                //-- recursively get the father’s family
                $this->printPersonPedigree($family->husband(), $count + 1);
                echo '</td>';
            } else {
                echo '<td>';
                if ($genoffset > $count) {
                    echo '<table cellspacing="0" cellpadding="0" border="0" >';
                    for ($i = 1; $i < ((2 ** ($genoffset - $count)) / 2); $i++) {
                        echo '<div class="wt-chart-box"></div>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            echo '</tr><tr>',
            '<td class="align-top"><img class="pvline" alt="" role="presentation" src="', e(asset('css/images/vline.png')), '" width="3" height="', $lh, '"></td>',
            '<td><img class="linef3" alt="" role="presentation" src="', e(asset('css/images/hline.png')), '" height="3"></td>',
            '<td>';
            //-- print the mother box
            echo FunctionsPrint::printPedigreePerson($family->wife());
            echo '</td>';
            if ($family->wife()) {
                echo '<td>';
                //-- recursively print the mother’s family
                $this->printPersonPedigree($family->wife(), $count + 1);
                echo '</td>';
            } else {
                echo '<td>';
                if ($count < $genoffset - 1) {
                    echo '<table cellspacing="0" cellpadding="0" border="0" >';
                    for ($i = 1; $i < ((2 ** (($genoffset - 1) - $count)) / 2) + 1; $i++) {
                        echo '<div class="wt-chart-box"></div>';
                        echo '</tr>';
                        echo '<div class="wt-chart-box"></div>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            echo '</tr>',
            '</table>';
            break;
        }
    }

    /**
     * Calculates number of generations a person has
     *
     * @param Individual $individual
     * @param int        $depth
     *
     * @return int
     */
    private function maxDescendencyGenerations(Individual $individual, $depth): int
    {
        if ($depth > $this->generations) {
            return $depth;
        }
        $maxdc = $depth;
        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->children() as $child) {
                $dc = $this->maxDescendencyGenerations($child, $depth + 1);
                if ($dc >= $this->generations) {
                    return $dc;
                }
                if ($dc > $maxdc) {
                    $maxdc = $dc;
                }
            }
        }
        $maxdc++;
        if ($maxdc == 1) {
            $maxdc++;
        }

        return $maxdc;
    }

    /**
     * Print a “Family Book” for an individual
     *
     * @param Individual $person
     * @param int        $descent_steps
     *
     * @return void
     */
    private function printFamilyBook(Individual $person, $descent_steps): void
    {
        if ($descent_steps == 0) {
            return;
        }

        echo
        '<h3>',
            /* I18N: %s is an individual’s name */
        I18N::translate('Family of %s', $person->fullName()),
        '</h3>',
        '<table cellspacing="0" cellpadding="0" border="0" ><tr><td class="align-middle">';
        $this->dgenerations = $this->generations;
        $this->printDescendency(1, $person);
        echo '</td><td class="align-middle">';
        $this->printPersonPedigree($person, 1);
        echo '</td></tr></table><br><br><hr class="wt-family-break"><br><br>';
        foreach ($person->spouseFamilies() as $family) {
            foreach ($family->children() as $child) {
                $this->printFamilyBook($child, $descent_steps - 1);
            }
        }
    }
}
