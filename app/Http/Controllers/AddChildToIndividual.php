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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class AddChildToIndividual
{
    use ViewResponseTrait;

    public function __construct(
        private readonly GedcomEditService $gedcom_edit_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        // Name facts.
        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($tree->getPreference('SURNAME_TRADITION'));

        $names = match ($individual->sex()) {
            Sex::Male   => $surname_tradition->newChildNames($individual, null, Sex::Unknown),
            Sex::Female => $surname_tradition->newChildNames(null, $individual, Sex::Unknown),
            default     => $surname_tradition->newChildNames(null, null, Sex::Unknown),
        };

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, Sex::Unknown, $names),
        ];

        $title = I18N::translate('Add a child to create a one-parent family');

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddChildToIndividual::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'title'               => $individual->fullName() . ' - ' . $title,
            'tree'                => $tree,
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $individual->url()),
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $levels = Validator::parsedBody($request)->list('ilevels');
        $tags   = Validator::parsedBody($request)->list('itags');
        $values = Validator::parsedBody($request)->list('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);

        // Create the new child
        $child  = $tree->createIndividual('0 @@ INDI' . $gedcom);

        // Create a new family
        $link   = $individual->sex() === Sex::Female ? 'WIFE' : 'HUSB';
        $gedcom = "0 @@ FAM\n1 " . $link . ' @' . $individual->xref() . "@\n1 CHIL @" . $child->xref() . '@';
        $family = $tree->createFamily($gedcom);

        // Link the individual to the family
        $individual->createFact('1 FAMS @' . $family->xref() . '@', false);

        // Link the child to the family
        $child->createFact('1 FAMC @' . $family->xref() . '@', false);

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $child->url());

        return redirect($url);
    }
}
