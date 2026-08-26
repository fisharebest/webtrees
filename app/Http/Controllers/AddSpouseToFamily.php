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
use Fisharebest\Webtrees\Enums\Sex;
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

final class AddSpouseToFamily
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

        $spouse = $family->spouses()->first();

        if ($spouse instanceof Individual) {
            $names = $surname_tradition->newSpouseNames($spouse, $sex);
        } else {
            $names = ['1 NAME ' . $surname_tradition->defaultName()];
        }

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, $sex, $names),
            'f' => $this->gedcom_edit_service->newFamilyFacts($tree),
        ];

        $title = match ($sex) {
            Sex::Female => I18N::translate('Add a wife'),
            Sex::Male   => I18N::translate('Add a husband'),
            default     => I18N::translate('Add a spouse'),
        };

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddSpouseToFamily::class, ['tree' => $tree->name(), 'xref' => $xref, 'sex' => $sex->value]),
            'title'               => $title,
            'tree'                => $tree,
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $family->url()),
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        // Create the new spouse
        $levels = Validator::parsedBody($request)->list('ilevels');
        $tags   = Validator::parsedBody($request)->list('itags');
        $values = Validator::parsedBody($request)->list('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);
        $spouse = $tree->createIndividual("0 @@ INDI\n1 FAMS @" . $family->xref() . '@' . $gedcom);

        // Link the spouse to the family
        $husb = $family->facts(['HUSB'], false, null, true)->first();
        $wife = $family->facts(['WIFE'], false, null, true)->first();

        if ($husb === null && $spouse->sex() === Sex::Male) {
            $link = 'HUSB';
        } elseif ($wife === null && $spouse->sex() === Sex::Female) {
            $link = 'WIFE';
        } elseif ($husb === null) {
            $link = 'HUSB';
        } elseif ($wife === null) {
            $link = 'WIFE';
        } else {
            // Family already has husband and wife
            return redirect($family->url());
        }

        // Link the spouse to the family
        $family->createFact('1 ' . $link . ' @' . $spouse->xref() . '@', false);

        // Add any family facts
        $levels = Validator::parsedBody($request)->list('flevels');
        $tags   = Validator::parsedBody($request)->list('ftags');
        $values = Validator::parsedBody($request)->list('fvalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Family::RECORD_TYPE, $levels, $tags, $values, false);

        if ($gedcom !== '') {
            $family->createFact($gedcom, false);
        }

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $spouse->url());

        return redirect($url);
    }
}
