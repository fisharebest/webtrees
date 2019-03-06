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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DescendancyModule
 */
class DescendancyModule extends AbstractModule implements ModuleSidebarInterface
{
    use ModuleSidebarTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/sidebar */
        return I18N::translate('Descendants');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Descendants” module */
        return I18N::translate('A sidebar showing the descendants of an individual.');
    }

    /**
     * The default position for this sidebar.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultSidebarOrder(): int
    {
        return 3;
    }

    /**
     * @param Request       $request
     * @param Tree          $tree
     * @param SearchService $search_service
     *
     * @return Response
     */
    public function getSearchAction(Request $request, Tree $tree, SearchService $search_service): Response
    {
        $search = $request->get('search', '');

        $html = '';

        if (strlen($search) >= 2) {
            $html = $search_service
                ->searchIndividualNames([$tree], [$search])
                ->map(function (Individual $individual): string {
                    return $this->getPersonLi($individual);
                })
                ->implode('');
        }

        if ($html !== '') {
            $html = '<ul>' . $html . '</ul>';
        }

        return new Response($html);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getDescendantsAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        if ($individual !== null && $individual->canShow()) {
            $html = $this->loadSpouses($individual, 1);
        } else {
            $html = '';
        }

        return new Response($html);
    }

    /** {@inheritdoc} */
    public function hasSidebarContent(Individual $individual): bool
    {
        return true;
    }

    /**
     * Load this sidebar synchronously.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getSidebarContent(Individual $individual): string
    {
        return view('modules/descendancy/sidebar', [
            'individual_list' => $this->getPersonLi($individual, 1),
        ]);
    }

    /**
     * Format an individual in a list.
     *
     * @param Individual $person
     * @param int        $generations
     *
     * @return string
     */
    public function getPersonLi(Individual $person, $generations = 0): string
    {
        $icon     = $generations > 0 ? 'icon-minus' : 'icon-plus';
        $lifespan = $person->canShow() ? '(' . $person->getLifeSpan() . ')' : '';
        $spouses  = $generations > 0 ? $this->loadSpouses($person, 0) : '';

        return
            '<li class="sb_desc_indi_li">' .
            '<a class="sb_desc_indi" href="' . e(route('module', [
                'module' => $this->name(),
                'action' => 'Descendants',
                'ged'    => $person->tree()->name(),
                'xref'   => $person->xref(),
            ])) . '">' .
            '<i class="plusminus ' . $icon . '"></i>' .
            $person->getSexImage() . $person->fullName() . $lifespan .
            '</a>' .
            '<a href="' . e($person->url()) . '" title="' . strip_tags($person->fullName()) . '">' . view('icons/individual') . '</a>' .
            '<div>' . $spouses . '</div>' .
            '</li>';
    }

    /**
     * Format a family in a list.
     *
     * @param Family     $family
     * @param Individual $person
     * @param int        $generations
     *
     * @return string
     */
    public function getFamilyLi(Family $family, Individual $person, $generations = 0): string
    {
        $spouse = $family->spouse($person);
        if ($spouse) {
            $spouse_name = $spouse->getSexImage() . $spouse->fullName();
            $spouse_link = '<a href="' . e($person->url()) . '" title="' . strip_tags($person->fullName()) . '">' . view('icons/individual') . '</a>';
        } else {
            $spouse_name = '';
            $spouse_link = '';
        }

        $family_link = '<a href="' . e($family->url()) . '" title="' . strip_tags($family->fullName()) . '">' . view('icons/family') . '</a>';

        $marryear = $family->getMarriageYear();
        $marr     = $marryear ? '<i class="icon-rings"></i>' . $marryear : '';

        return
            '<li class="sb_desc_indi_li">' .
            '<a class="sb_desc_indi" href="#"><i class="plusminus icon-minus"></i>' .
            $spouse_name .
            $marr .
            '</a>' .
            $spouse_link .
            $family_link .
            '<div>' . $this->loadChildren($family, $generations) . '</div>' .
            '</li>';
    }

    /**
     * Display spouses.
     *
     * @param Individual $individual
     * @param int        $generations
     *
     * @return string
     */
    public function loadSpouses(Individual $individual, $generations)
    {
        $out = '';
        if ($individual->canShow()) {
            foreach ($individual->spouseFamilies() as $family) {
                $out .= $this->getFamilyLi($family, $individual, $generations - 1);
            }
        }
        if ($out) {
            return '<ul>' . $out . '</ul>';
        }

        return '';
    }

    /**
     * Display descendants.
     *
     * @param Family $family
     * @param int    $generations
     *
     * @return string
     */
    public function loadChildren(Family $family, $generations)
    {
        $out = '';
        if ($family->canShow()) {
            $children = $family->children();

            if ($children->isNotEmpty()) {
                foreach ($children as $child) {
                    $out .= $this->getPersonLi($child, $generations - 1);
                }
            } else {
                $out .= '<li class="sb_desc_none">' . I18N::translate('No children') . '</li>';
            }
        }
        if ($out) {
            return '<ul>' . $out . '</ul>';
        }

        return '';
    }
}
