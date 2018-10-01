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

namespace Fisharebest\Webtrees\Module\InteractiveTree;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Class TreeView
 */
class TreeView
{
    /** @var string HTML element name */
    private $name;

    /**
     * Treeview Constructor
     *
     * @param string $name the name of the TreeView object’s instance
     */
    public function __construct($name = 'tree')
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
     * @return string[]  HTML and Javascript
     */
    public function drawViewport(Individual $individual, $generations): array
    {
        $html = view('interactive-tree-chart', [
            'name'       => $this->name,
            'individual' => $this->drawPerson($individual, $generations, 0, null, null, true),
        ]);

        return [
            $html,
            'var ' . $this->name . 'Handler = new TreeViewHandler("' . $this->name . '", "' . e($individual->getTree()->getName()) . '");',
        ];
    }

    /**
     * Return a JSON structure to a JSON request
     *
     * @param Tree   $tree
     * @param string $list list of JSON requests
     *
     * @return string
     */
    public function getPersons(Tree $tree, $list): string
    {
        $list = explode(';', $list);
        $r    = [];
        foreach ($list as $jsonRequest) {
            $firstLetter = substr($jsonRequest, 0, 1);
            $jsonRequest = substr($jsonRequest, 1);
            switch ($firstLetter) {
                case 'c':
                    $fidlist = explode(',', $jsonRequest);
                    $flist   = [];
                    foreach ($fidlist as $fid) {
                        $flist[] = Family::getInstance($fid, $tree);
                    }
                    $r[] = $this->drawChildren($flist, 1, true);
                    break;
                case 'p':
                    $params = explode('@', $jsonRequest);
                    $fid    = $params[0];
                    $order  = $params[1];
                    $f      = Family::getInstance($fid, $tree);
                    if ($f->getHusband()) {
                        $r[] = $this->drawPerson($f->getHusband(), 0, 1, $f, $order);
                    } elseif ($f->getWife()) {
                        $r[] = $this->drawPerson($f->getWife(), 0, 1, $f, $order);
                    }
                    break;
            }
        }

        return json_encode($r);
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
        foreach ($individual->getSpouseFamilies() as $family) {
            $spouse = $family->getSpouse($individual);
            if ($spouse) {
                $html .= $this->getPersonDetails($spouse, $family);
            }
        }

        return $html;
    }

    /**
     * Return the details for a person
     *
     * @param Individual $individual
     * @param Family     $family
     *
     * @return string
     */
    private function getPersonDetails(Individual $individual, Family $family = null): string
    {
        $chart_url = route('module', [
            'module' => 'tree',
            'action' => 'Treeview',
            'xref'   => $individual->getXref(),
            'ged'    => $individual->getTree()->getName(),
        ]);

        $hmtl = $this->getThumbnail($individual);
        $hmtl .= '<a class="tv_link" href="' . e($individual->url()) . '">' . $individual->getFullName() . '</a> <a href="' . e($chart_url) . '" title="' . I18N::translate('Interactive tree of %s', strip_tags($individual->getFullName())) . '" class="wt-icon-individual tv_link tv_treelink"></a>';
        foreach ($individual->getFacts(WT_EVENTS_BIRT, true) as $fact) {
            $hmtl .= $fact->summary();
        }
        if ($family) {
            foreach ($family->getFacts(WT_EVENTS_MARR, true) as $fact) {
                $hmtl .= $fact->summary();
            }
        }
        foreach ($individual->getFacts(WT_EVENTS_DEAT, true) as $fact) {
            $hmtl .= $fact->summary();
        }

        return '<div class="tv' . $individual->getSex() . ' tv_person_expanded">' . $hmtl . '</div>';
    }

    /**
     * Draw the children for some families
     *
     * @param Family[] $familyList array of families to draw the children for
     * @param int      $gen        number of generations to draw
     * @param bool     $ajax       setted to true for an ajax call
     *
     * @return string
     */
    private function drawChildren(array $familyList, $gen = 1, $ajax = false): string
    {
        $html          = '';
        $children2draw = [];
        $f2load        = [];

        foreach ($familyList as $f) {
            if (empty($f)) {
                continue;
            }
            $children = $f->getChildren();
            if ($children) {
                $f2load[] = $f->getXref();
                foreach ($children as $child) {
                    // Eliminate duplicates - e.g. when adopted by a step-parent
                    $children2draw[$child->getXref()] = $child;
                }
            }
        }
        $tc = count($children2draw);
        if ($tc) {
            $f2load = implode(',', $f2load);
            $nbc    = 0;
            foreach ($children2draw as $child) {
                $nbc++;
                if ($tc == 1) {
                    $co = 'c'; // unique
                } elseif ($nbc == 1) {
                    $co = 't'; // first
                } elseif ($nbc == $tc) {
                    $co = 'b'; //last
                } else {
                    $co = 'h';
                }
                $html .= $this->drawPerson($child, $gen - 1, -1, null, $co);
            }
            if (!$ajax) {
                $html = '<td align="right"' . ($gen == 0 ? ' abbr="c' . $f2load . '"' : '') . '>' . $html . '</td>' . $this->drawHorizontalLine();
            }
        }

        return $html;
    }

    /**
     * Draw a person in the tree
     *
     * @param Individual $person The Person object to draw the box for
     * @param int        $gen    The number of generations up or down to print
     * @param int        $state  Whether we are going up or down the tree, -1 for descendents +1 for ancestors
     * @param Family     $pfamily
     * @param string     $order  first (1), last(2), unique(0), or empty. Required for drawing lines between boxes
     * @param bool       $isRoot
     *
     * @return string
     *
     * Notes : "spouse" means explicitely married partners. Thus, the word "partner"
     * (for "life partner") here fits much better than "spouse" or "mate"
     * to translate properly the modern french meaning of "conjoint"
     */
    private function drawPerson(Individual $person, $gen, $state, Family $pfamily = null, $order = null, $isRoot = false): string
    {
        if ($gen < 0) {
            return '';
        }
        if (!empty($pfamily)) {
            $partner = $pfamily->getSpouse($person);
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
            $html .= $this->drawChildren($person->getSpouseFamilies(), $gen);
        } else {
            // draw the parent’s lines
            $html .= $this->drawVerticalLine($order) . $this->drawHorizontalLine();
        }

        /* draw the person. Do NOT add person or family id as an id, since a same person could appear more than once in the tree !!! */
        // Fixing the width for td to the box initial width when the person is the root person fix a rare bug that happen when a person without child and without known parents is the root person : an unwanted white rectangle appear at the right of the person’s boxes, otherwise.
        $html .= '<td' . ($isRoot ? ' style="width:1px"' : '') . '><div class="tv_box' . ($isRoot ? ' rootPerson' : '') . '" dir="' . I18N::direction() . '" style="text-align: ' . (I18N::direction() === 'rtl' ? 'right' : 'left') . '; direction: ' . I18N::direction() . '" abbr="' . $person->getXref() . '" onclick="' . $this->name . 'Handler.expandBox(this, event);">';
        $html .= $this->drawPersonName($person);
        $fop = []; // $fop is fathers of partners
        if ($partner !== null) {
            $dashed = '';
            foreach ($person->getSpouseFamilies() as $family) {
                $spouse = $family->getSpouse($person);
                if ($spouse) {
                    $spouse_parents = $spouse->getPrimaryChildFamily();
                    if ($spouse_parents && $spouse_parents->getHusband()) {
                        $fop[] = [
                            $spouse_parents->getHusband(),
                            $spouse_parents,
                        ];
                    } elseif ($spouse_parents && $spouse_parents->getWife()) {
                        $fop[] = [
                            $spouse_parents->getWife(),
                            $spouse_parents,
                        ];
                    }
                    $html .= $this->drawPersonName($spouse, $dashed);
                    $dashed = 'dashed';
                }
            }
        }
        $html .= '</div></td>';

        $primaryChildFamily = $person->getPrimaryChildFamily();
        if (!empty($primaryChildFamily)) {
            $parent = $primaryChildFamily->getHusband();
            if (empty($parent)) {
                $parent = $primaryChildFamily->getWife();
            }
        }
        if (!empty($parent) || count($fop) || ($state < 0)) {
            $html .= $this->drawHorizontalLine();
        }
        /* draw the parents */
        if ($state >= 0 && (!empty($parent) || count($fop))) {
            $unique = (empty($parent) || count($fop) == 0);
            $html .= '<td align="left"><table class="tv_tree"><tbody>';
            if (!empty($parent)) {
                $u = $unique ? 'c' : 't';
                $html .= '<tr><td ' . ($gen == 0 ? ' abbr="p' . $primaryChildFamily->getXref() . '@' . $u . '"' : '') . '>';
                $html .= $this->drawPerson($parent, $gen - 1, 1, $primaryChildFamily, $u);
                $html .= '</td></tr>';
            }
            if (count($fop)) {
                $n  = 0;
                $nb = count($fop);
                foreach ($fop as $p) {
                    $n++;
                    $u = $unique ? 'c' : ($n == $nb || empty($p[1]) ? 'b' : 'h');
                    $html .= '<tr><td ' . ($gen == 0 ? ' abbr="p' . $p[1]->getXref() . '@' . $u . '"' : '') . '>' . $this->drawPerson($p[0], $gen - 1, 1, $p[1], $u) . '</td></tr>';
                }
            }
            $html .= '</tbody></table></td>';
        }
        if ($state < 0) {
            $html .= $this->drawVerticalLine($order);
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
     * @param Individual $individual an individual
     * @param string     $dashed     if = 'dashed' print dashed top border to separate multiple spuses
     *
     * @return string
     */
    private function drawPersonName(Individual $individual, $dashed = ''): string
    {
        $family = $individual->getPrimaryChildFamily();
        if ($family) {
            $family_name = strip_tags($family->getFullName());
        } else {
            $family_name = I18N::translateContext('unknown family', 'unknown');
        }
        switch ($individual->getSex()) {
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
        $sex = $individual->getSex();

        return '<div class="tv' . $sex . ' ' . $dashed . '"' . $title . '><a href="' . e($individual->url()) . '"></a>' . $individual->getFullName() . ' <span class="dates">' . $individual->getLifeSpan() . '</span></div>';
    }

    /**
     * Get the thumbnail image for the given person
     *
     * @param Individual $individual
     *
     * @return string
     */
    private function getThumbnail(Individual $individual)
    {
        if ($individual->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
            return $individual->displayImage(40, 50, 'crop', []);
        }

        return '';
    }

    /**
     * Draw a vertical line
     *
     * @param string $order A parameter that set how to draw this line with auto-redimensionning capabilities
     *
     * @return string
     * WARNING : some tricky hacks are required in CSS to ensure cross-browser compliance
     * some browsers shows an image, which imply a size limit in height,
     * and some other browsers (ex: firefox) shows a <div> tag, which have no size limit in height
     * Therefore, Firefox is a good choice to print very big trees.
     */
    private function drawVerticalLine($order): string
    {
        return '<td class="tv_vline tv_vline_' . $order . '"><div class="tv_vline tv_vline_' . $order . '"></div></td>';
    }

    /**
     * Draw an horizontal line
     */
    private function drawHorizontalLine(): string
    {
        return '<td class="tv_hline"><div class="tv_hline"></div></td>';
    }
}
