<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Link an existing individual as child in an existing family.
 */
class LinkChildToFamilyAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $params = (array) $request->getParsedBody();

        $famid = $params['famid'];

        $family = Registry::familyFactory()->make($famid, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $PEDI = $params['PEDI'];

        // Replace any existing child->family link (we may be changing the PEDI);
        $fact_id = '';
        foreach ($individual->facts(['FAMC']) as $fact) {
            if ($family === $fact->target()) {
                $fact_id = $fact->id();
                break;
            }
        }

        switch ($PEDI) {
            case '':
                $gedcom = "1 FAMC @$famid@";
                break;
            case 'adopted':
                $gedcom = "1 FAMC @$famid@\n2 PEDI $PEDI\n1 ADOP\n2 FAMC @$famid@\n3 ADOP BOTH";
                break;
            case 'sealing':
                $gedcom = "1 FAMC @$famid@\n2 PEDI $PEDI\n1 SLGC\n2 FAMC @$famid@";
                break;
            case 'foster':
                $gedcom = "1 FAMC @$famid@\n2 PEDI $PEDI\n1 EVEN\n2 TYPE $PEDI";
                break;
            default:
                $gedcom = "1 FAMC @$famid@\n2 PEDI $PEDI";
                break;
        }

        $individual->updateFact($fact_id, $gedcom, true);

        // Only set the family->child link if it does not already exist
        $chil_link_exists = false;
        foreach ($family->facts(['CHIL']) as $fact) {
            if ($individual === $fact->target()) {
                $chil_link_exists = true;
                break;
            }
        }

        if (!$chil_link_exists) {
            $family->createFact('1 CHIL @' . $individual->xref() . '@', true);
        }

        return redirect($individual->url());
    }
}
