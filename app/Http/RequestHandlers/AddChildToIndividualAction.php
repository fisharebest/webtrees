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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

final class AddChildToIndividualAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly GedcomEditService $gedcom_edit_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::attributes($request)->isXref()->string('xref');
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $levels = Validator::parsedBody($request)->array('ilevels');
        $tags   = Validator::parsedBody($request)->array('itags');
        $values = Validator::parsedBody($request)->array('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);

        // Create the new child
        $child  = $tree->createIndividual('0 @@ INDI' . $gedcom);

        // Create a new family
        $link   = $individual->sex() === 'F' ? 'WIFE' : 'HUSB';
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
