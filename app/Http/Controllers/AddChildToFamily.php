<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class AddChildToFamily
{
    use ViewResponseTrait;

    public function __construct(
        private GedcomEditService $gedcom_edit_service
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $sex    = Validator::attributes($request)->string('sex');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $sex = Sex::from($sex);

        // Name facts.
        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($tree->getPreference('SURNAME_TRADITION'));

        $names = $surname_tradition->newChildNames($family->husband(), $family->wife(), $sex);

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, $sex, $names),
        ];

        $title = match ($sex) {
            Sex::Male   => I18N::translate('Add a son'),
            Sex::Female => I18N::translate('Add a daughter'),
            default     => I18N::translate('Add a child'),
        };

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddChildToFamily::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'title'               => $family->fullName() . ' - ' . $title,
            'tree'                => $tree,
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $family->url()),
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $levels = Validator::parsedBody($request)->list('ilevels');
        $tags   = Validator::parsedBody($request)->list('itags');
        $values = Validator::parsedBody($request)->list('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);

        // Create the new child
        $child  = $tree->createIndividual("0 @@ INDI\n1 FAMC @" . $xref . '@' . $gedcom);

        // Link the child to the family
        $before = $this->childFactOfYoungerSibling($family, $child);
        $family->createFact('1 CHIL @' . $child->xref() . '@', true, $before);

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $child->url());

        return redirect($url);
    }

    private function childFactOfYoungerSibling(Family $family, Individual $child): Fact | null
    {
        $filter = function (Fact $fact) use ($child): bool {
            return $fact->target() instanceof Individual &&
                Date::compare($child->getBirthDate(), $fact->target()->getBirthDate()) < 0;
        };
        return $family
            ->facts(['CHIL'], false, AccessLevel::Hidden, true)
            ->first($filter);
    }
}
