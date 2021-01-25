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
use function redirect;

/**
 * Link an existing individual as a new spouse.
 */
class LinkSpouseToIndividualAction implements RequestHandlerInterface
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

        $xref = $request->getQueryParams()['xref'];

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $params = (array) $request->getParsedBody();
        $spid   = $params['spid'];

        $spouse = Registry::individualFactory()->make($spid, $tree);
        $spouse = Auth::checkIndividualAccess($spouse, true);

        if ($individual->sex() === 'M') {
            $gedcom = "0 @@ FAM\n1 HUSB @" . $individual->xref() . "@\n1 WIFE @" . $spouse->xref() . '@';
        } else {
            $gedcom = "0 @@ FAM\n1 WIFE @" . $individual->xref() . "@\n1 HUSB @" . $spouse->xref() . '@';
        }

        $gedcom .= $this->gedcom_edit_service->addNewFact($request, $tree, 'MARR');

        $family = $tree->createFamily($gedcom);

        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', true);

        return redirect($family->url());
    }
}
