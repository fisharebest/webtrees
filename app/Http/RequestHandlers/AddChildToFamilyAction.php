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
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

class AddChildToFamilyAction implements RequestHandlerInterface
{
    public function __construct(
        private GedcomEditService $gedcom_edit_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $levels = Validator::parsedBody($request)->array('ilevels');
        $tags   = Validator::parsedBody($request)->array('itags');
        $values = Validator::parsedBody($request)->array('ivalues');
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
            ->facts(['CHIL'], false, Auth::PRIV_HIDE, true)
            ->first($filter);
    }
}
