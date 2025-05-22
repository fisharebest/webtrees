<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module\InteractiveTree;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function count;

use const JSON_THROW_ON_ERROR;

class TreeView
{
    // HTML element name
    private string $name;

    /**
     * Treeview Constructor
     *
     * @param string $name the name of the TreeView object’s instance
     */
    public function __construct(string $name = 'tree')
    {
        $this->name = $name;
    }

    /**
     * Draw the viewport which creates the draggable/zoomable framework
     * Size is set by the container, as the viewport can scale itself automatically
     *
     * @param Individual $individual  Draw the chart for this individual
     * @param int        $generations number of generations to draw
     *
     * @return array<string>  HTML and Javascript
     */
    public function drawViewport(Individual $individual, int $generations): array
    {
        $html = view('modules/interactive-tree/chart', [
            'module'     => 'tree',
            'name'       => $this->name,
            'individual' => $this->drawPerson($individual, $generations, 0, null, '', true),
            'tree'       => $individual->tree(),
        ]);

        return [
            $html,
            'var ' . $this->name . 'Handler = new TreeViewHandler("' . $this->name . '", "' . e($individual->tree()->name()) . '");',
        ];
    }

    /**
     * Return a JSON structure to a JSON request
     *
     * @param Tree   $tree
     * @param string $request list of JSON requests
     *
     * @return string
     */
    public function getIndividuals(Tree $tree, string $request): string
    {
        $json_requests = explode(';', $request);
        $r             = [];

        foreach ($json_requests as $json_request) {
            $firstLetter = substr($json_request, 0, 1);
            $json_request = substr($json_request, 1);

            switch ($firstLetter) {
                case 'c':
                    $families = Collection::make(explode(',', $json_request))
                        ->map(static fn (string $xref): Family|null => Registry::familyFactory()->make($xref, $tree))
                        ->filter();

                    $r[] = $this->drawChildren($families, 1, true);
                    break;

                case 'p':
                    [$xref, $order] = explode('@', $json_request);

                    $family = Registry::familyFactory()->make($xref, $tree);
                    if ($family instanceof Family) {
                        // Prefer the paternal line
                        $parent = $family->husband() ?? $family->wife();

                        // The family may have no parents (just children).
                        if ($parent instanceof Individual) {
                            $r[] = $this->drawPerson($parent, 0, 1, $family, $order, false);
                        }
                    }
                    break;
            }
        }

        return json_encode($r, JSON_THROW_ON_ERROR);
    }

    /**
     * Get the details for a person and their life partner(s)
     *
     * @param Individual $individual the individual to return the details for
     *
     * @return string
     */
    public function getDetails(Individual $individual): string
    {
        $html = $this->getPersonDetails($individual, null);
        foreach ($individual->spouseFamilies() as $family) {
            $spouse = $family->spouse($individual);
            if ($spouse) {
                $html .= $this->getPersonDetails($spouse, $family);
            }
        }

        return $html;
    }

    /**
     * Return the details for a person
     *
     * @param Individual  $individual
     * @param Family|null $family
     *
     * @return string
     */
    private function getPersonDetails(Individual $individual, Family|null $family = null): string
    {
        $chart_url = route('module', [
            'module' => 'tree',
            'action' => 'Chart',
            'xref'   => $individual->xref(),
            'tree'   => $individual->tree()->name(),
        ]);

        $hmtl = $this->getThumbnail($individual);
        $hmtl .= '<a class="tv_link" href="' . e($individual->url()) . '">' . $individual->fullName() . '</a> <a href="' . e($chart_url) . '" title="' . I18N::translate('Interactive tree of %s', strip_tags($individual->fullName())) . '" class="tv_link tv_treelink">' . view('icons/individual') . '</a>';
        foreach ($individual->facts(Gedcom::BIRTH_EVENTS, true) as $fact) {
            $hmtl .= $fact->summary();
        }
        if ($family instanceof Family) {
            foreach ($family->facts(Gedcom::MARRIAGE_EVENTS, true) as $fact) {
                $hmtl .= $fact->summary();
            }
        }
        foreach ($individual->facts(Gedcom::DEATH_EVENTS, true) as $fact) {
            $hmtl .= $fact->summary();
        }

        return '<div class="tv' . $individual->sex() . ' tv_person_expanded">' . $hmtl . '</div>';
    }

    /**
     * Draw the children for some families
     *
     * @param Collection<int,Family> $familyList array of families to draw the children for
     * @param int                    $gen        number of generations to draw
     * @param bool                   $ajax       true for an ajax call
     *
     * @return string
     */
    private function drawChildren(Collection $familyList, int $gen = 1, bool $ajax = false): string
    {
        $html          = '';
        $children2draw = [];
        $f2load        = [];

        foreach ($familyList as $f) {
            $children = $f->children();
            if ($children->isNotEmpty()) {
                $f2load[] = $f->xref();
                foreach ($children as $child) {
                    // Eliminate duplicates - e.g. when adopted by a step-parent
                    $children2draw[$child->xref()] = $child;
                }
            }
        }
        $tc = count($children2draw);
        if ($tc > 0) {
            $f2load = implode(',', $f2load);
            $nbc    = 0;
            foreach ($children2draw as $child) {
                $nbc++;
                if ($tc === 1) {
                    $co = 'c'; // unique
                } elseif ($nbc === 1) {
                    $co = 't'; // first
                } elseif ($nbc === $tc) {
                    $co = 'b'; //last
                } else {
                    $co = 'h';
                }
                $html .= $this->drawPerson($child, $gen - 1, -1, null, $co, false);
            }
            if (!$ajax) {
                $html = '<td align="right"' . ($gen === 0 ? ' abbr="c' . $f2load . '"' : '') . '>' . $html . '</td>' . $this->drawHorizontalLine();
            }
        }

        return $html;
    }

    /**
     * Draw a person in the tree
     *
     * @param Individual  $person The Person object to draw the box for
     * @param int         $gen    The number of generations up or down to print
     * @param int         $state  Whether we are going up or down the tree, -1 for descendents +1 for ancestors
     * @param Family|null $pfamily
     * @param string      $line   b, c, h, t. Required for drawing lines between boxes
     * @param bool        $isRoot
     *
     * @return string
     */
    private function drawPerson(Individual $person, int $gen, int $state, Family|null $pfamily, string $line, bool $isRoot): string
    {
        if ($gen < 0) {
            return '';
        }

        if ($pfamily instanceof Family) {
            $partner = $pfamily->spouse($person);
        } else {
            $partner = $person->getCurrentSpouse();
        }

        if ($isRoot) {
            $html = '<table id="tvTreeBorder" class="tv_tree"><tbody><tr><td id="tv_tree_topleft"></td><td id="tv_tree_top"></td><td id="tv_tree_topright"></td></tr><tr><td id="tv_tree_left"></td><td>';
        } else {
            $html = '';
        }
        /* height 1% : this hack enable the div auto-dimensioning in td for FF & Chrome */
        $html .= '<table class="tv_tree"' . ($isRoot ? ' id="tv_tree"' : '') . ' style="height: 1%"><tbody><tr>';

        if ($state <= 0) {
            // draw children
            $html .= $this->drawChildren($person->spouseFamilies(), $gen);
        } else {
            // draw the parent’s lines
            $html .= $this->drawVerticalLine($line) . $this->drawHorizontalLine();
        }

        /* draw the person. Do NOT add person or family id as an id, since a same person could appear more than once in the tree !!! */
        // Fixing the width for td to the box initial width when the person is the root person fix a rare bug that happen when a person without child and without known parents is the root person : an unwanted white rectangle appear at the right of the person’s boxes, otherwise.
        $html .= '<td' . ($isRoot ? ' style="width:1px"' : '') . '><div class="tv_box' . ($isRoot ? ' rootPerson' : '') . '" dir="' . I18N::direction() . '" style="text-align: ' . (I18N::direction() === 'rtl' ? 'right' : 'left') . '; direction: ' . I18N::direction() . '" abbr="' . $person->xref() . '" onclick="' . $this->name . 'Handler.expandBox(this, event);">';
        $html .= $this->drawPersonName($person, '');

        $fop = []; // $fop is fathers of partners

        if ($partner !== null) {
            $dashed = '';
            foreach ($person->spouseFamilies() as $family) {
                $spouse = $family->spouse($person);
                if ($spouse instanceof Individual) {
                    $spouse_parents = $spouse->childFamilies()->first();
                    if ($spouse_parents instanceof Family) {
                        $spouse_parent = $spouse_parents->husband() ?? $spouse_parents->wife();

                        if ($spouse_parent instanceof Individual) {
                            $fop[] = [$spouse_parent, $spouse_parents];
                        }
                    }

                    $html .= $this->drawPersonName($spouse, $dashed);
                    $dashed = 'dashed';
                }
            }
        }
        $html .= '</div></td>';

        $primaryChildFamily = $person->childFamilies()->first();
        if ($primaryChildFamily instanceof Family) {
            $parent = $primaryChildFamily->husband() ?? $primaryChildFamily->wife();
        } else {
            $parent = null;
        }

        if ($parent instanceof Individual || $fop !== [] || $state < 0) {
            $html .= $this->drawHorizontalLine();
        }

        /* draw the parents */
        if ($state >= 0 && ($parent instanceof Individual || $fop !== [])) {
            $unique = $parent === null || $fop === [];
            $html .= '<td align="left"><table class="tv_tree"><tbody>';

            if ($parent instanceof Individual) {
                $u = $unique ? 'c' : 't';
                $html .= '<tr><td ' . ($gen === 0 ? ' abbr="p' . $primaryChildFamily->xref() . '@' . $u . '"' : '') . '>';
                $html .= $this->drawPerson($parent, $gen - 1, 1, $primaryChildFamily, $u, false);
                $html .= '</td></tr>';
            }

            if ($fop !== []) {
                $n  = 0;
                $nb = count($fop);
                foreach ($fop as $p) {
                    $n++;
                    $u = $unique ? 'c' : ($n === $nb || empty($p[1]) ? 'b' : 'h');
                    $html .= '<tr><td ' . ($gen === 0 ? ' abbr="p' . $p[1]->xref() . '@' . $u . '"' : '') . '>' . $this->drawPerson($p[0], $gen - 1, 1, $p[1], $u, false) . '</td></tr>';
                }
            }
            $html .= '</tbody></table></td>';
        }

        if ($state < 0) {
            $html .= $this->drawVerticalLine($line);
        }

        $html .= '</tr></tbody></table>';

        if ($isRoot) {
            $html .= '</td><td id="tv_tree_right"></td></tr><tr><td id="tv_tree_bottomleft"></td><td id="tv_tree_bottom"></td><td id="tv_tree_bottomright"></td></tr></tbody></table>';
        }

        return $html;
    }

    /**
     * Draw a person name preceded by sex icon, with parents as tooltip
     *
     * @param Individual $individual The individual to draw
     * @param string     $dashed     Either "dashed", to print dashed top border to separate multiple spouses, or ""
     *
     * @return string
     */
    private function drawPersonName(Individual $individual, string $dashed): string
    {
        $family = $individual->childFamilies()->first();
        if ($family) {
            $family_name = strip_tags($family->fullName());
        } else {
            $family_name = I18N::translateContext('unknown family', 'unknown');
        }
        switch ($individual->sex()) {
            case 'M':
                /* I18N: e.g. “Son of [father name & mother name]” */
                $title = ' title="' . I18N::translate('Son of %s', $family_name) . '"';
                break;
            case 'F':
                /* I18N: e.g. “Daughter of [father name & mother name]” */
                $title = ' title="' . I18N::translate('Daughter of %s', $family_name) . '"';
                break;
            default:
                /* I18N: e.g. “Child of [father name & mother name]” */
                $title = ' title="' . I18N::translate('Child of %s', $family_name) . '"';
                break;
        }
        $sex = $individual->sex();

        return '<div class="tv' . $sex . ' ' . $dashed . '"' . $title . '><a href="' . e($individual->url()) . '"></a>' . $individual->fullName() . ' <span class="dates">' . $individual->lifespan() . '</span></div>';
    }

    /**
     * Get the thumbnail image for the given person
     *
     * @param Individual $individual
     *
     * @return string
     */
    private function getThumbnail(Individual $individual): string
    {
        if ($individual->tree()->getPreference('SHOW_HIGHLIGHT_IMAGES') !== '' && $individual->tree()->getPreference('SHOW_HIGHLIGHT_IMAGES') !== '0') {
            return $individual->displayImage(40, 50, 'crop', []);
        }

        return '';
    }

    /**
     * Draw a vertical line
     *
     * @param string $line A parameter that set how to draw this line with auto-resizing capabilities
     *
     * @return string
     * WARNING : some tricky hacks are required in CSS to ensure cross-browser compliance
     * some browsers shows an image, which imply a size limit in height,
     * and some other browsers (ex: firefox) shows a <div> tag, which have no size limit in height
     * Therefore, Firefox is a good choice to print very big trees.
     */
    private function drawVerticalLine(string $line): string
    {
        return '<td class="tv_vline tv_vline_' . $line . '"><div class="tv_vline tv_vline_' . $line . '"></div></td>';
    }

    /**
     * Draw an horizontal line
     */
    private function drawHorizontalLine(): string
    {
        return '<td class="tv_hline"><div class="tv_hline"></div></td>';
    }
}
