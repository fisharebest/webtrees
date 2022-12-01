<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function strlen;
use function view;

/**
 * Class DescendancyModule
 */
class DescendancyModule extends AbstractModule implements ModuleSidebarInterface
{
    use ModuleSidebarTrait;

    private SearchService $search_service;

    /**
     * DescendancyModule constructor.
     *
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getSearchAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $search = Validator::queryParams($request)->string('search');

        $html = '';

        if (strlen($search) >= 2) {
            $html = $this->search_service
                ->searchIndividualNames([$tree], [$search])
                ->map(function (Individual $individual): string {
                    return $this->getPersonLi($individual);
                })
                ->implode('');
        }

        if ($html !== '') {
            $html = '<ul>' . $html . '</ul>';
        }

        return response($html);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getDescendantsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $xref = Validator::queryParams($request)->isXref()->string('xref');

        $individual = Registry::individualFactory()->make($xref, $tree);

        if ($individual !== null && $individual->canShow()) {
            $html = $this->loadSpouses($individual, 1);
        } else {
            $html = '';
        }

        return response($html);
    }

    /**
     * @param Individual $individual
     *
     * @return bool
     */
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
            'tree'            => $individual->tree(),
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
    public function getPersonLi(Individual $person, int $generations = 0): string
    {
        $icon     = $generations > 0 ? 'icon-minus' : 'icon-plus';
        $lifespan = $person->canShow() ? '(' . $person->lifespan() . ')' : '';
        $spouses  = $generations > 0 ? $this->loadSpouses($person, 0) : '';

        return
            '<li class="sb_desc_indi_li">' .
            '<a class="sb_desc_indi" href="#" data-wt-href="' . e(route('module', [
                'module' => $this->name(),
                'action' => 'Descendants',
                'tree'    => $person->tree()->name(),
                'xref'   => $person->xref(),
            ])) . '">' .
            '<i class="plusminus ' . $icon . '"></i>' .
            '<small>' . view('icons/sex', ['sex' => $person->sex()]) . '</small>' . $person->fullName() . $lifespan .
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
    public function getFamilyLi(Family $family, Individual $person, int $generations = 0): string
    {
        $spouse = $family->spouse($person);
        if ($spouse instanceof Individual) {
            $spouse_name = '<small>' . view('icons/sex', ['sex' => $spouse->sex()]) . '</small>' . $spouse->fullName();
            $spouse_link = '<a href="' . e($spouse->url()) . '" title="' . strip_tags($spouse->fullName()) . '">' . view('icons/individual') . '</a>';
        } else {
            $spouse_name = '';
            $spouse_link = '';
        }

        $family_link = '<a href="' . e($family->url()) . '" title="' . strip_tags($family->fullName()) . '">' . view('icons/family') . '</a>';

        $marryear = $family->getMarriageYear();
        $marr     = $marryear ? '<i class="icon-rings"></i>' . $marryear : '';

        return
            '<li class="sb_desc_indi_li">' .
            '<a class="sb_desc_indi" href="#" data-wt-href="#"><i class="plusminus icon-minus"></i>' .
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
    public function loadSpouses(Individual $individual, int $generations): string
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
    public function loadChildren(Family $family, int $generations): string
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
