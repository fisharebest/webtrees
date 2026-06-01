<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

/**
 * Snapshot of the GEDCOM-record state that handlers must restore when
 * exiting a nested <Gedcom>...</Gedcom> scope.
 *
 * Replaces the previous anonymous tuple
 * [$this->gedrec, $this->fact, $this->desc] pushed onto $gedrec_stack.
 */
final readonly class GedcomFrame
{
    public function __construct(
        public string $gedrec,
        public string $fact,
        public string $desc,
    ) {
    }
}
