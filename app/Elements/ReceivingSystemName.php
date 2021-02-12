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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Tree;

/**
 * RECEIVING_SYSTEM_NAME := {Size=1:20}
 * The name of the system expected to process the GEDCOM-compatible
 * transmission. The registered RECEIVING_SYSTEM_NAME for all GEDCOM
 * submissions to the Family History Department must be one of the following
 * names:
 * ! "ANSTFILE" when submitting to Ancestral File.
 * ! "TempleReady" when submitting for temple ordinance clearance.
 */
class ReceivingSystemName extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 20;

    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        return 'DISKETTE';
    }
}
