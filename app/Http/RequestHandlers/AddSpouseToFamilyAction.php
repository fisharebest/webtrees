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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function preg_match_all;
use function redirect;
use function trim;

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

        $xref   = $request->getQueryParams()['xref'];
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $params = (array) $request->getParsedBody();

        $this->gedcom_edit_service->glevels = $params['glevels'] ?? [];
        $this->gedcom_edit_service->tag     = $params['tag'] ?? [];
        $this->gedcom_edit_service->text    = $params['text'] ?? [];
        $this->gedcom_edit_service->islink  = $params['islink'] ?? [];

        // Create the new spouse
        $this->gedcom_edit_service->splitSource(); // separate SOUR record from the rest

        $gedrec = '0 @@ INDI';
        $gedrec .= $this->gedcom_edit_service->addNewName($request, $tree);
        $gedrec .= $this->gedcom_edit_service->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }

        if ($params['SOUR_INDI'] ?? false) {
            $gedrec = $this->gedcom_edit_service->handleUpdates($gedrec);
        } else {
            $gedrec = $this->gedcom_edit_service->updateRest($gedrec);
        }
        $gedrec .= "\n1 FAMS @" . $family->xref() . '@';
        $spouse = $tree->createIndividual($gedrec);

        // Update the existing family - add marriage, etc
        if ($family->facts(['HUSB'])->first() instanceof Fact) {
            $family->createFact('1 WIFE @' . $spouse->xref() . '@', true);
        } else {
            $family->createFact('1 HUSB @' . $spouse->xref() . '@', true);
        }
        $famrec = '';
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $famrec .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_FAM'] ?? false) {
            $famrec = $this->gedcom_edit_service->handleUpdates($famrec);
        } else {
            $famrec = $this->gedcom_edit_service->updateRest($famrec);
        }
        $family->createFact(trim($famrec), true); // trim leading \n

        if (($params['goto'] ?? '') === 'new') {
            return redirect($spouse->url());
        }

        return redirect($family->url());
    }
}
