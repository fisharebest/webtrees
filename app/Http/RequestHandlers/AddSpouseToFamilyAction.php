<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Add a new spouse to a family.
 */
class AddSpouseToFamilyAction implements RequestHandlerInterface
{
    private GedcomEditService $gedcom_edit_service;

    /**
     * @param GedcomEditService $gedcom_edit_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        // Create the new spouse
        $levels = Validator::parsedBody($request)->array('ilevels');
        $tags   = Validator::parsedBody($request)->array('itags');
        $values = Validator::parsedBody($request)->array('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);
        $spouse = $tree->createIndividual("0 @@ INDI\n1 FAMS @" . $family->xref() . '@' . $gedcom);

        // Link the spouse to the family
        $husb = $family->facts(['HUSB'], false, null, true)->first();
        $wife = $family->facts(['WIFE'], false, null, true)->first();

        if ($husb === null && $spouse->sex() === 'M') {
            $link = 'HUSB';
        } elseif ($wife === null && $spouse->sex() === 'F') {
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
        $levels = Validator::parsedBody($request)->array('flevels');
        $tags   = Validator::parsedBody($request)->array('ftags');
        $values = Validator::parsedBody($request)->array('fvalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Family::RECORD_TYPE, $levels, $tags, $values);

        if ($gedcom !== '') {
            $family->createFact($gedcom, false);
        }

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $spouse->url());

        return redirect($url);
    }
}
