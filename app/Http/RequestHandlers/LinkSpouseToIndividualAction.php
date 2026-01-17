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

final class LinkSpouseToIndividualAction implements RequestHandlerInterface
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

        $levels = Validator::parsedBody($request)->array('flevels');
        $tags   = Validator::parsedBody($request)->array('ftags');
        $values = Validator::parsedBody($request)->array('fvalues');

        // Create the new family
        $spid   = Validator::parsedBody($request)->string('spid');
        $spouse = Registry::individualFactory()->make($spid, $tree);
        $spouse = Auth::checkIndividualAccess($spouse, true);

        if ($individual->sex() === 'M') {
            $gedcom = "0 @@ FAM\n1 HUSB @" . $individual->xref() . "@\n1 WIFE @" . $spouse->xref() . '@';
        } else {
            $gedcom = "0 @@ FAM\n1 WIFE @" . $individual->xref() . "@\n1 HUSB @" . $spouse->xref() . '@';
        }

        $gedcom .= $this->gedcom_edit_service->editLinesToGedcom(Family::RECORD_TYPE, $levels, $tags, $values);

        $family = $tree->createFamily($gedcom);

        // Link the individual to the family
        $before = $this->famsFactOfLaterMarriage($individual, $family);
        $individual->createFact('1 FAMS @' . $family->xref() . '@', true, $before);

        // Link the spouse to the family
        $before = $this->famsFactOfLaterMarriage($spouse, $family);
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', true, $before);

        return redirect($family->url());
    }

    private function famsFactOfLaterMarriage(Individual $partner, Family $family): Fact | null
    {
        $filter = function (Fact $fact) use ($family): bool {
            return $fact->target() instanceof Family &&
                Date::compare($family->getMarriageDate(), $fact->target()->getMarriageDate()) < 0;
        };
        return $partner
            ->facts(['FAMS'], false, Auth::PRIV_HIDE, true)
            ->first($filter);
    }
}
