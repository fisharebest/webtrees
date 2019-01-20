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

use DomainException;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line ancestors in a tree layout.
 */
class PedigreeChartController extends AbstractChartController
{
    // With more than 8 generations, we run out of pixels on the <canvas>
    private const MAX_GENERATIONS = 8;
    private const MIN_GENERATIONS = 2;

    private const DEFAULT_GENERATIONS = 4;

    /**
     * Chart orientation codes
     * Dont change them! the offset calculations rely on this order
     */
    public const PORTRAIT         = 0;
    public const LANDSCAPE        = 1;
    public const OLDEST_AT_TOP    = 2;
    public const OLDEST_AT_BOTTOM = 3;

    private const DEFAULT_ORIENTATION = self::LANDSCAPE;

    /** @var int Selected chart layout */
    private $orientation;

    /** @var int Number of generation to display */
    private $generations;

    /** @var array data pertaining to each chart node */
    private $nodes = [];

    /** @var int Number of nodes in the chart */
    private $treesize;

    /** @var bool Are there ancestors beyond the bounds of this chart */
    private $chartHasAncestors = false;

    /** @var stdClass Determine which arrows to use for each of the chart orientations */
    private $arrows;

    /** @var Individual */
    private $root;

    /**
     * Next and previous generation arrow size
     */
    private const ARROW_SIZE = 22; //pixels

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
        $this->checkModuleIsActive($tree, PedigreeChartModule::class);

        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        $this->checkIndividualAccess($individual);

        $orientation = (int) $request->get('orientation', self::DEFAULT_ORIENTATION);
        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min(self::MAX_GENERATIONS, $generations);
        $generations = max(self::MIN_GENERATIONS, $generations);

        $orientations       = $this->orientations();
        $generation_options = $this->generationOptions();

        /* I18N: %s is an individual’s name */
        $title = I18N::translate('Pedigree tree of %s', $individual->getFullName());

        return $this->viewResponse('pedigree-page', [
            'generations'        => $generations,
            'generation_options' => $generation_options,
            'individual'         => $individual,
            'orientation'        => $orientation,
            'orientations'       => $orientations,
            'title'              => $title,
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
        $this->checkModuleIsActive($tree, PedigreeChartModule::class);

        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        $this->checkIndividualAccess($individual);

        $this->orientation = (int) $request->get('orientation');
        $this->generations = (int) $request->get('generations');
        $bxspacing         = Theme::theme()->parameter('chart-spacing-x');
        $byspacing         = Theme::theme()->parameter('chart-spacing-y');
        $curgen            = 1; // -- track which generation the algorithm is currently working on
        $addoffset         = [];

        // With more than 8 generations, we run out of pixels on the <canvas>
        if ($this->generations > 8) {
            $this->generations = 8;
        }

        $this->root = $individual;

        $this->treesize = (2 ** $this->generations) - 1;

        $this->nodes = [];

        $ancestors = $chart_service->sosaStradonitzAncestors($individual, $this->generations);

        // $ancestors starts array at index 1 we need to start at 0
        for ($i = 0; $i < $this->treesize; ++$i) {
            $this->nodes[$i] = [
                'indi' => $ancestors->get($i+1),
                'x' => 0,
                'y' => 0,
            ];
        }

        //check earliest generation for any ancestors
        for ($i = (int) ($this->treesize / 2); $i < $this->treesize; $i++) {
            $this->chartHasAncestors = $this->chartHasAncestors || ($this->nodes[$i]['indi'] && $this->nodes[$i]['indi']->getChildFamilies());
        }

        $this->arrows = new stdClass();
        switch ($this->orientation) {
            case self::PORTRAIT:
            case self::LANDSCAPE:
                $this->arrows->prevGen = 'fas fa-arrow-end wt-icon-arrow-end';
                $this->arrows->menu    = 'fas fa-arrow-start wt-icon-arrow-start';
                $addoffset['x']        = $this->chartHasAncestors ? self::ARROW_SIZE : 0;
                $addoffset['y']        = 0;
                break;

            case self::OLDEST_AT_TOP:
                $this->arrows->prevGen = 'fas fa-arrow-up wt-icon-arrow-up';
                $this->arrows->menu    = 'fas fa-arrow-down wt-icon-arrow-down';
                $addoffset['x']        = 0;
                $addoffset['y']        = $this->root->getSpouseFamilies() ? self::ARROW_SIZE : 0;
                break;

            case self::OLDEST_AT_BOTTOM:
                $this->arrows->prevGen = 'fas fa-arrow-down wt-icon-arrow-down';
                $this->arrows->menu    = 'fas fa-arrow-up wt-icon-arrow-up';
                $addoffset['x']        = 0;
                $addoffset['y']        = $this->chartHasAncestors ? self::ARROW_SIZE : 0;
                break;

            default:
                throw new DomainException('Invalid orientation: ' . $this->orientation);
        }

        // -- this next section will create and position the DIV layers for the pedigree tree
        // -- loop through all of IDs in the array from last to first
        // -- calculating the box positions

        for ($i = ($this->treesize - 1); $i >= 0; $i--) {
            // -- check to see if we have moved to the next generation
            if ($i < (int) ($this->treesize / (2 ** $curgen))) {
                $curgen++;
            }

            // -- box position in current generation
            $boxpos = $i - (2 ** ($this->generations - $curgen));
            // -- offset multiple for current generation
            if ($this->orientation < self::OLDEST_AT_TOP) {
                $genoffset  = 2 ** ($curgen - $this->orientation);
                $boxspacing = Theme::theme()->parameter('chart-box-y') + $byspacing;
            } else {
                $genoffset  = 2 ** ($curgen - 1);
                $boxspacing = Theme::theme()->parameter('chart-box-x') + $byspacing;
            }
            // -- calculate the yoffset position in the generation put child between parents
            $yoffset = ($boxpos * ($boxspacing * $genoffset)) + (($boxspacing / 2) * $genoffset) + ($boxspacing * $genoffset);

            // -- calculate the xoffset
            switch ($this->orientation) {
                case self::PORTRAIT:
                    $xoffset = ($this->generations - $curgen) * ((Theme::theme()->parameter('chart-box-x') + $bxspacing) / 1.8);
                    if (!$i && $this->root->getSpouseFamilies()) {
                        $xoffset -= self::ARROW_SIZE;
                    }
                    // -- compact the tree
                    if ($curgen < $this->generations) {
                        if ($i % 2 == 0) {
                            $yoffset = $yoffset - (($boxspacing / 2) * ($curgen - 1));
                        } else {
                            $yoffset = $yoffset + (($boxspacing / 2) * ($curgen - 1));
                        }
                        $parent = (int) (($i - 1) / 2);
                        $pgen   = $curgen;
                        while ($parent > 0) {
                            if ($parent % 2 == 0) {
                                $yoffset = $yoffset - (($boxspacing / 2) * $pgen);
                            } else {
                                $yoffset = $yoffset + (($boxspacing / 2) * $pgen);
                            }
                            $pgen++;
                            if ($pgen > 3) {
                                $temp = 0;
                                for ($j = 1; $j < ($pgen - 2); $j++) {
                                    $temp += ((2 ** $j) - 1);
                                }
                                if ($parent % 2 == 0) {
                                    $yoffset = $yoffset - (($boxspacing / 2) * $temp);
                                } else {
                                    $yoffset = $yoffset + (($boxspacing / 2) * $temp);
                                }
                            }
                            $parent = (int) (($parent - 1) / 2);
                        }
                        if ($curgen > 3) {
                            $temp = 0;
                            for ($j = 1; $j < ($curgen - 2); $j++) {
                                $temp += ((2 ** $j) - 1);
                            }
                            if ($i % 2 == 0) {
                                $yoffset = $yoffset - (($boxspacing / 2) * $temp);
                            } else {
                                $yoffset = $yoffset + (($boxspacing / 2) * $temp);
                            }
                        }
                    }
                    $yoffset -= (($boxspacing / 2) * (2 ** ($this->generations - 2)) - ($boxspacing / 2));
                    break;
                case self::LANDSCAPE:
                    $xoffset = ($this->generations - $curgen) * (Theme::theme()->parameter('chart-box-x') + $bxspacing);
                    if ($curgen == 1) {
                        $xoffset += 10;
                    }
                    break;
                case self::OLDEST_AT_TOP:
                    //swap x & y offsets as chart is rotated
                    $xoffset = $yoffset;
                    $yoffset = $curgen * (Theme::theme()->parameter('chart-box-y') + ($byspacing * 4));
                    break;
                case self::OLDEST_AT_BOTTOM:
                    //swap x & y offsets as chart is rotated
                    $xoffset = $yoffset;
                    $yoffset = ($this->generations - $curgen) * (Theme::theme()->parameter('chart-box-y') + ($byspacing * 2));
                    if ($i && $this->root->getSpouseFamilies()) {
                        $yoffset += self::ARROW_SIZE;
                    }
                    break;

                default:
                    throw new DomainException('Invalid orientation: ' . $this->orientation);
            }
            $this->nodes[$i]['x'] = (int) $xoffset;
            $this->nodes[$i]['y'] = (int) $yoffset;
        }

        // find the minimum x & y offsets and deduct that number from
        // each value in the array so that offsets start from zero

        $min_xoffset = min(array_map(function (array $item): int {
            return $item['x'];
        }, $this->nodes));
        $min_yoffset = min(array_map(function (array $item): int {
            return $item['y'];
        }, $this->nodes));

        array_walk($this->nodes, function (&$item) use ($min_xoffset, $min_yoffset) {
            $item['x'] -= $min_xoffset;
            $item['y'] -= $min_yoffset;
        });

        // calculate chart & canvas dimensions
        $max_xoffset = max(array_map(function ($item) {
            return $item['x'];
        }, $this->nodes));
        $max_yoffset = max(array_map(function ($item) {
            return $item['y'];
        }, $this->nodes));

        $canvas_width   = $max_xoffset + $bxspacing + Theme::theme()->parameter('chart-box-x') + $addoffset['x'];
        $canvas_height  = $max_yoffset + $byspacing + Theme::theme()->parameter('chart-box-y') + $addoffset['y'];
        $posn           = I18N::direction() === 'rtl' ? 'right' : 'left';
        $last_gen_start = (int) floor($this->treesize / 2);
        if ($this->orientation === self::OLDEST_AT_TOP || $this->orientation === self::OLDEST_AT_BOTTOM) {
            $flex_direction = ' flex-column';
        } else {
            $flex_direction = '';
        }

        foreach ($this->nodes as $n => $node) {
            if ($n >= $last_gen_start) {
                $this->nodes[$n]['previous_gen'] = $this->gotoPreviousGen($n);
            } else {
                $this->nodes[$n]['previous_gen'] = '';
            }
        }


        $html = view('pedigree-chart', [
            'canvas_height'    => $canvas_height,
            'canvas_width'     => $canvas_width,
            'child_menu'       => $this->getMenu(),
            'flex_direction'   => $flex_direction,
            'last_gen_start'   => $last_gen_start,
            'orientation'      => $this->orientation,
            'nodes'            => $this->nodes,
            'landscape'        => self::LANDSCAPE,
            'oldest_at_top'    => self::OLDEST_AT_TOP,
            'oldest_at_bottom' => self::OLDEST_AT_BOTTOM,
            'portrait'         => self::PORTRAIT,
            'posn'             => $posn,
        ]);

        return new Response($html);
    }

    /**
     * Function get_menu
     *
     * Build a menu for the chart root individual
     *
     * @return string
     */
    public function getMenu(): string
    {
        $families = $this->root->getSpouseFamilies();
        $html     = '';
        if (!empty($families)) {
            $html = sprintf('<div id="childarrow"><a href="#" class="menuselect %s"></a><div id="childbox-pedigree">', $this->arrows->menu);

            foreach ($families as $family) {
                $html .= '<span class="name1">' . I18N::translate('Family') . '</span>';
                $spouse = $family->getSpouse($this->root);
                if ($spouse) {
                    $html .= '<a class="name1" href="' . e(route('pedigree', [
                            'xref'        => $spouse->xref(),
                            'ged'         => $spouse->tree()->name(),
                            'generations' => $this->generations,
                            'orientation' => $this->orientation,
                        ])) . '">' . $spouse->getFullName() . '</a>';
                }
                $children = $family->getChildren();
                foreach ($children as $sibling) {
                    $html .= '<a class="name1" href="' . e(route('pedigree', [
                            'xref'        => $sibling->xref(),
                            'ged'         => $sibling->tree()->name(),
                            'generations' => $this->generations,
                            'orientation' => $this->orientation,
                        ])) . '">' . $sibling->getFullName() . '</a>';
                }
            }
            //-- echo the siblings
            foreach ($this->root->getChildFamilies() as $family) {
                $siblings = array_filter($family->getChildren(), function (Individual $item): bool {
                    return $this->root->xref() !== $item->xref();
                });
                if (!empty($siblings)) {
                    $html .= '<span class="name1">';
                    $html .= count($siblings) > 1 ? I18N::translate('Siblings') : I18N::translate('Sibling');
                    $html .= '</span>';
                    foreach ($siblings as $sibling) {
                        $html .= '<a class="name1" href="' . e(route('pedigree', [
                                'xref'        => $sibling->xref(),
                                'ged'         => $sibling->tree()->name(),
                                'generations' => $this->generations,
                                'orientation' => $this->orientation,
                            ])) . '">' . $sibling->getFullName() . '</a>';
                    }
                }
            }
            $html .=
                '</div>' . // #childbox-pedigree
                '</div>'; // #childarrow
        }

        return $html;
    }

    /**
     * Function gotoPreviousGen
     *
     * Create a link to generate a new chart based on the correct parent of the individual with this index
     *
     * @param int $index
     *
     * @return string
     */
    public function gotoPreviousGen($index): string
    {
        $html = '';
        if ($this->chartHasAncestors) {
            if ($this->nodes[$index]['indi'] && $this->nodes[$index]['indi']->getChildFamilies()) {
                $html .= '<div class="ancestorarrow">';
                $rootParentId = 1;
                if ($index > (int) ($this->treesize / 2) + (int) ($this->treesize / 4)) {
                    $rootParentId++;
                }
                $html .= '<a class="' . $this->arrows->prevGen . '" href="' . e(route('pedigree', [
                        'xref'        => $this->nodes[$rootParentId]['indi']->xref(),
                        'ged'         => $this->nodes[$rootParentId]['indi']->tree()->name(),
                        'generations' => $this->generations,
                        'orientation' => $this->orientation,
                    ])) . '"></a>';
                $html .= '</div>';
            } else {
                $html .= '<div class="spacer"></div>';
            }
        }

        return $html;
    }


    /**
     * @return string[]
     */
    private function generationOptions(): array
    {
        return FunctionsEdit::numericOptions(range(self::MIN_GENERATIONS, self::MAX_GENERATIONS));
    }

    /**
     * @return string[]
     */
    private function orientations(): array
    {
        return [
            0 => I18N::translate('Portrait'),
            1 => I18N::translate('Landscape'),
            2 => I18N::translate('Oldest at top'),
            3 => I18N::translate('Oldest at bottom'),
        ];
    }
}
