<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
use function preg_match_all;
use function redirect;

/**
 * Add a new spouse to an individual, creating a new family.
 */
class AddSpouseToIndividualAction implements RequestHandlerInterface
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

        $sex = $params['SEX'];

        $this->gedcom_edit_service->glevels = $params['glevels'] ?? [];
        $this->gedcom_edit_service->tag     = $params['tag'] ?? [];
        $this->gedcom_edit_service->text    = $params['text'] ?? [];
        $this->gedcom_edit_service->islink  = $params['islink'] ?? [];

        $this->gedcom_edit_service->splitSource();
        $indi_gedcom = '0 @@ INDI';
        $indi_gedcom .= $this->gedcom_edit_service->addNewName($request, $tree);
        $indi_gedcom .= $this->gedcom_edit_service->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $indi_gedcom .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_INDI'] ?? false) {
            $indi_gedcom = $this->gedcom_edit_service->handleUpdates($indi_gedcom);
        } else {
            $indi_gedcom = $this->gedcom_edit_service->updateRest($indi_gedcom);
        }

        $fam_gedcom = '';
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $fam_gedcom .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_FAM'] ?? false) {
            $fam_gedcom = $this->gedcom_edit_service->handleUpdates($fam_gedcom);
        } else {
            $fam_gedcom = $this->gedcom_edit_service->updateRest($fam_gedcom);
        }

        // Create the new spouse
        $spouse = $tree->createIndividual($indi_gedcom);
        // Create a new family
        if ($sex === 'F') {
            $family = $tree->createFamily("0 @@ FAM\n1 WIFE @" . $spouse->xref() . "@\n1 HUSB @" . $individual->xref() . '@' . $fam_gedcom);
        } else {
            $family = $tree->createFamily("0 @@ FAM\n1 HUSB @" . $spouse->xref() . "@\n1 WIFE @" . $individual->xref() . '@' . $fam_gedcom);
        }
        // Link the spouses to the family
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', true);
        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);

        if (($params['goto'] ?? '') === 'new') {
            return redirect($spouse->url());
        }

        return redirect($individual->url());
    }
}
