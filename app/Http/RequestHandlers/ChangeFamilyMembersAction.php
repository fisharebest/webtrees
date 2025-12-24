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
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;
use function redirect;

/**
 * Change the members of a family.
 */
class ChangeFamilyMembersAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::parsedBody($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $HUSB = Validator::parsedBody($request)->isXref()->string('HUSB', '');
        $WIFE = Validator::parsedBody($request)->isXref()->string('WIFE', '');
        $CHIL = Validator::parsedBody($request)->array('CHIL');

        // Current family members
        $old_father   = $family->husband();
        $old_mother   = $family->wife();
        $old_children = $family->children();

        // New family members
        $new_father   = Registry::individualFactory()->make($HUSB, $tree);
        $new_mother   = Registry::individualFactory()->make($WIFE, $tree);
        $new_children = [];
        foreach ($CHIL as $child) {
            $new_children[] = Registry::individualFactory()->make($child, $tree);
        }

        if ($old_father !== $new_father) {
            if ($old_father instanceof Individual) {
                // Remove old FAMS link
                foreach ($old_father->facts(['FAMS']) as $fact) {
                    if ($fact->target() === $family) {
                        $old_father->deleteFact($fact->id(), true);
                    }
                }
                // Remove old HUSB link
                foreach ($family->facts(['HUSB', 'WIFE']) as $fact) {
                    if ($fact->target() === $old_father) {
                        $family->deleteFact($fact->id(), true);
                    }
                }
            }
            if ($new_father instanceof Individual) {
                // Add new FAMS link
                $before = $this->famsFactOfLaterMarriage($new_father, $family);
                $new_father->createFact('1 FAMS @' . $family->xref() . '@', true, $before);
                // Add new HUSB link
                $family->createFact('1 HUSB @' . $new_father->xref() . '@', true);
            }
        }

        if ($old_mother !== $new_mother) {
            if ($old_mother instanceof Individual) {
                // Remove old FAMS link
                foreach ($old_mother->facts(['FAMS']) as $fact) {
                    if ($fact->target() === $family) {
                        $old_mother->deleteFact($fact->id(), true);
                    }
                }
                // Remove old WIFE link
                foreach ($family->facts(['HUSB', 'WIFE']) as $fact) {
                    if ($fact->target() === $old_mother) {
                        $family->deleteFact($fact->id(), true);
                    }
                }
            }
            if ($new_mother instanceof Individual) {
                // Add new FAMS link
                $before = $this->famsFactOfLaterMarriage($new_mother, $family);
                $new_mother->createFact('1 FAMS @' . $family->xref() . '@', true, $before);
                // Add new WIFE link
                $family->createFact('1 WIFE @' . $new_mother->xref() . '@', true);
            }
        }

        foreach ($old_children as $old_child) {
            if (!in_array($old_child, $new_children, true)) {
                // Remove old FAMC link
                foreach ($old_child->facts(['FAMC']) as $fact) {
                    if ($fact->target() === $family) {
                        $old_child->deleteFact($fact->id(), true);
                    }
                }
                // Remove old CHIL link
                foreach ($family->facts(['CHIL']) as $fact) {
                    if ($fact->target() === $old_child) {
                        $family->deleteFact($fact->id(), true);
                    }
                }
            }
        }

        foreach ($new_children as $new_child) {
            if ($new_child instanceof Individual && !$old_children->contains($new_child)) {
                // Add new FAMC link
                $new_child->createFact('1 FAMC @' . $family->xref() . '@', true);
                // Add new CHIL link
                $before = $this->childFactOfYoungerSibling($family, $new_child);
                $family->createFact('1 CHIL @' . $new_child->xref() . '@', true, $before);
            }
        }

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
