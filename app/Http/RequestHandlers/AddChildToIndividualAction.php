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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function preg_match_all;
use function redirect;

/**
 * Add a new child to an individual, creating a one-parent family.
 */
class AddChildToIndividualAction implements RequestHandlerInterface
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

        $individual = Factory::individual()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $params = (array) $request->getParsedBody();

        $PEDI = $params['PEDI'];

        $this->gedcom_edit_service->glevels = $params['glevels'] ?? [];
        $this->gedcom_edit_service->tag     = $params['tag'] ?? [];
        $this->gedcom_edit_service->text    = $params['text'] ?? [];
        $this->gedcom_edit_service->islink  = $params['islink'] ?? [];

        // Create a family
        if ($individual->sex() === 'F') {
            $gedcom = "0 @@ FAM\n1 WIFE @" . $individual->xref() . '@';
        } else {
            $gedcom = "0 @@ FAM\n1 HUSB @" . $individual->xref() . '@';
        }
        $family = $tree->createFamily($gedcom);

        // Link the parent to the family
        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);

        // Create a child
        $this->gedcom_edit_service->splitSource(); // separate SOUR record from the rest

        $gedcom = '0 @@ INDI';
        $gedcom .= $this->gedcom_edit_service->addNewName($request, $tree);
        $gedcom .= $this->gedcom_edit_service->addNewSex($request);
        $gedcom .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $family->xref());
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedcom .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_INDI'] ?? false) {
            $gedcom = $this->gedcom_edit_service->handleUpdates($gedcom);
        } else {
            $gedcom = $this->gedcom_edit_service->updateRest($gedcom);
        }

        $child = $tree->createIndividual($gedcom);

        // Link the family to the child
        $family->createFact('1 CHIL @' . $child->xref() . '@', true);

        if (($params['goto'] ?? '') === 'new') {
            return redirect($child->url());
        }

        return redirect($individual->url());
    }
}
