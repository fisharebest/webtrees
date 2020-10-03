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
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\Individual;
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
 * Add a new child to a family.
 */
class AddChildToFamilyAction implements RequestHandlerInterface
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

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $params = (array) $request->getParsedBody();

        $PEDI      = $params['PEDI'];
        $keep_chan = (bool) ($params['keep_chan'] ?? false);

        $this->gedcom_edit_service->glevels = $params['glevels'] ?? [];
        $this->gedcom_edit_service->tag     = $params['tag'] ?? [];
        $this->gedcom_edit_service->text    = $params['text'] ?? [];
        $this->gedcom_edit_service->islink  = $params['islink'] ?? [];

        $this->gedcom_edit_service->splitSource();
        $gedrec = '0 @@ INDI';
        $gedrec .= $this->gedcom_edit_service->addNewName($request, $tree);
        $gedrec .= $this->gedcom_edit_service->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        $gedrec .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $xref);
        if ($params['SOUR_INDI'] ?? false) {
            $gedrec = $this->gedcom_edit_service->handleUpdates($gedrec);
        } else {
            $gedrec = $this->gedcom_edit_service->updateRest($gedrec);
        }

        // Create the new child
        $new_child = $tree->createIndividual($gedrec);

        // Insert new child at the right place
        $done = false;
        foreach ($family->facts(['CHIL']) as $fact) {
            $old_child = $fact->target();
            if ($old_child instanceof Individual && Date::compare($new_child->getEstimatedBirthDate(), $old_child->getEstimatedBirthDate()) < 0) {
                // Insert before this child
                $family->updateFact($fact->id(), '1 CHIL @' . $new_child->xref() . "@\n" . $fact->gedcom(), !$keep_chan);
                $done = true;
                break;
            }
        }
        if (!$done) {
            // Append child at end
            $family->createFact('1 CHIL @' . $new_child->xref() . '@', !$keep_chan);
        }

        if (($params['goto'] ?? '') === 'new') {
            return redirect($new_child->url());
        }

        return redirect($family->url());
    }
}
