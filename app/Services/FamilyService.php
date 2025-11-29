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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;

use function implode;

class FamilyService
{
    /**
     * Add a child to a family.
     *
     * @param Individual $child
     * @param Family $family
     *
     * @return void
     */
    public function addChildToFamily(Individual $child, Family $family): void
    {
        $child_birth_day = $child->getBirthDate()->julianDay();
        $child_gedcom = '1 CHIL @' . $child->xref() . '@';
        $family_facts = ['0 @' . $family->xref() . '@ FAM'];

        // Insert new child at the right place
        $done = false;
        foreach ($family->facts() as $fact) {
            if ($fact->tag() === 'FAM:CHIL' && !$done) {
                // insert new child when born before this child
                if ($child_birth_day < $fact->target()->getBirthDate()->julianDay()) {
                    $family_facts[] = $child_gedcom;
                    $done = true;
                }
            }
            $family_facts[] = $fact->gedcom();
        }
        if (!$done) {
            // Append child at end
            $family_facts[] = $child_gedcom;
        }

        $gedcom = implode("\n", $family_facts);
        $family->updateRecord($gedcom, false);
    }
}
