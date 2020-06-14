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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Controller for edit forms and responses.
 */
class EditFamilyController extends AbstractEditController
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addChild(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $family = Factory::family()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $gender = $request->getQueryParams()['gender'];

        $subtitles = [
            'M' => I18N::translate('Add a son'),
            'F' => I18N::translate('Add a daughter'),
            'U' => I18N::translate('Add a child'),
        ];

        $subtitle = $subtitles[$gender] ?? $subtitles['U'];

        $title = $family->fullName() . ' - ' . $subtitle;

        return $this->viewResponse('edit/new-individual', [
            'next_action' => 'add-child-to-family-action',
            'tree'       => $tree,
            'title'      => $title,
            'individual' => null,
            'family'     => $family,
            'name_fact'  => null,
            'famtag'     => 'CHIL',
            'gender'     => $gender,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addChildAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $family = Factory::family()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $params = (array) $request->getParsedBody();

        $PEDI      = $params['PEDI'];
        $keep_chan = (bool) ($params['keep_chan'] ?? false);

        $this->glevels = $params['glevels'] ?? [];
        $this->tag     = $params['tag'] ?? [];
        $this->text    = $params['text'] ?? [];
        $this->islink  = $params['islink'] ?? [];

        $this->splitSource();
        $gedrec = '0 @@ INDI';
        $gedrec .= $this->addNewName($request, $tree);
        $gedrec .= $this->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->addNewFact($request, $tree, $match);
            }
        }
        $gedrec .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $xref);
        if ($params['SOUR_INDI'] ?? false) {
            $gedrec = $this->handleUpdates($gedrec);
        } else {
            $gedrec = $this->updateRest($gedrec);
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addSpouse(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref   = $request->getQueryParams()['xref'];
        $famtag = $request->getQueryParams()['famtag'];
        $family = Factory::family()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        if ($famtag === 'WIFE') {
            $title  = I18N::translate('Add a wife');
            $gender = 'F';
        } else {
            $title  = I18N::translate('Add a husband');
            $gender = 'M';
        }

        return $this->viewResponse('edit/new-individual', [
            'next_action' => 'add-spouse-to-family-action',
            'tree'       => $tree,
            'title'      => $title,
            'individual' => null,
            'family'     => $family,
            'name_fact'  => null,
            'famtag'     => $famtag,
            'gender'     => $gender,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addSpouseAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref   = $request->getQueryParams()['xref'];
        $family = Factory::family()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $params = (array) $request->getParsedBody();

        $this->glevels = $params['glevels'] ?? [];
        $this->tag     = $params['tag'] ?? [];
        $this->text    = $params['text'] ?? [];
        $this->islink  = $params['islink'] ?? [];

        // Create the new spouse
        $this->splitSource(); // separate SOUR record from the rest

        $gedrec = '0 @@ INDI';
        $gedrec .= $this->addNewName($request, $tree);
        $gedrec .= $this->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->addNewFact($request, $tree, $match);
            }
        }

        if ($params['SOUR_INDI'] ?? false) {
            $gedrec = $this->handleUpdates($gedrec);
        } else {
            $gedrec = $this->updateRest($gedrec);
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
                $famrec .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_FAM'] ?? false) {
            $famrec = $this->handleUpdates($famrec);
        } else {
            $famrec = $this->updateRest($famrec);
        }
        $family->createFact(trim($famrec), true); // trim leading \n

        if (($params['goto'] ?? '') === 'new') {
            return redirect($spouse->url());
        }

        return redirect($family->url());
    }
}
