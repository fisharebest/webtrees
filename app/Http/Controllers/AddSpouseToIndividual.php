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

final class AddSpouseToIndividual
{
    use ViewResponseTrait;

    public function __construct(
        private GedcomEditService $gedcom_edit_service
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $sex = $individual->sex()->opposite();

        // Name facts.
        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($tree->getPreference('SURNAME_TRADITION'));

        $names = $surname_tradition->newSpouseNames($individual, $sex);

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, $sex, $names),
            'f' => $this->gedcom_edit_service->newFamilyFacts($tree),
        ];

        $title = match ($sex) {
            Sex::Female => I18N::translate('Add a wife'),
            Sex::Male => I18N::translate('Add a husband'),
            default => I18N::translate('Add a spouse'),
        };

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddSpouseToIndividual::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'title'               => $individual->fullName() . ' - ' . $title,
            'tree'                => $tree,
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $individual->url()),
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        // Create the new spouse
        $levels = Validator::parsedBody($request)->list('ilevels');
        $tags   = Validator::parsedBody($request)->list('itags');
        $values = Validator::parsedBody($request)->list('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);
        $spouse = $tree->createIndividual('0 @@ INDI' . $gedcom);

        // Create the new family
        $levels = Validator::parsedBody($request)->list('flevels');
        $tags   = Validator::parsedBody($request)->list('ftags');
        $values = Validator::parsedBody($request)->list('fvalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Family::RECORD_TYPE, $levels, $tags, $values);
        $i_link = "\n1 " . ($individual->sex() === Sex::Female ? 'WIFE' : 'HUSB') . ' @' . $individual->xref() . '@';
        $s_link = "\n1 " . ($individual->sex() !== Sex::Female ? 'WIFE' : 'HUSB') . ' @' . $spouse->xref() . '@';
        $family = $tree->createFamily('0 @@ FAM' . $gedcom . $i_link . $s_link);

        // Link the individual to the family
        $before = $this->famsFactOfLaterMarriage($individual, $family);
        $individual->createFact('1 FAMS @' . $family->xref() . '@', false, $before);

        // Link the spouse to the family
        $before = $this->famsFactOfLaterMarriage($spouse, $family);
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', false, $before);

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $spouse->url());

        return redirect($url);
    }

    private function famsFactOfLaterMarriage(Individual $partner, Family $family): Fact | null
    {
        $filter = function (Fact $fact) use ($family): bool {
            return $fact->target() instanceof Family &&
                Date::compare($family->getMarriageDate(), $fact->target()->getMarriageDate()) < 0;
        };
        return $partner
            ->facts(['FAMS'], false, AccessLevel::Hidden, true)
            ->first($filter);
    }
}
