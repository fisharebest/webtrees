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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Add a new parent to an individual, creating a one-parent family.
 */
class AddParentToIndividualAction implements RequestHandlerInterface
{
    private GedcomEditService $gedcom_edit_service;

    /**
     * AddChildToFamilyAction constructor.
     *
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
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $params     = (array) $request->getParsedBody();
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $levels = $params['ilevels'] ?? [];
        $tags   = $params['itags'] ?? [];
        $values = $params['ivalues'] ?? [];

        // Create the new parent
        $gedcom = "0 @@ INDI\n" . $this->gedcom_edit_service->editLinesToGedcom('INDI', $levels, $tags, $values);
        $parent = $tree->createIndividual($gedcom);

        // Create a new family
        $link   = $parent->sex() === 'F' ? 'WIFE' : 'HUSB';
        $gedcom = "0 @@ FAM\n1 CHIL @" . $individual->xref() . "@\n1 " . $link . ' @' . $parent->xref() . '@';
        $family = $tree->createFamily($gedcom);

        // Link the individual to the family
        $individual->createFact('1 FAMC @' . $family->xref() . '@', false);

        // Link the parent to the family
        $parent->createFact('1 FAMS @' . $family->xref() . '@', false);

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $parent->url());

        return redirect($url);
    }
}
