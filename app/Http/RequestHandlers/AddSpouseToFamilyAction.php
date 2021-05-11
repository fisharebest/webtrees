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
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Add a new spouse to a family.
 */
class AddSpouseToFamilyAction implements RequestHandlerInterface
{
    /** @var GedcomEditService */
    private $gedcom_edit_service;

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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $params = (array) $request->getParsedBody();

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $levels = $params['ilevels'] ?? [];
        $tags   = $params['itags'] ?? [];
        $values = $params['ivalues'] ?? [];

        // Create the new spouse
        $gedcom = "0 @@ INDI\n1 FAMS @" . $family->xref() . "@\n" . $this->gedcom_edit_service->editLinesToGedcom('INDI', $levels, $tags, $values);
        $spouse = $tree->createIndividual($gedcom);

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

        return redirect($params['url'] ?? $spouse->url());
    }
}
