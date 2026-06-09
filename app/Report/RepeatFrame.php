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
 * Snapshot of the repeat-loop state pushed when entering nested
 * <RepeatTag>, <Facts>, <List> or <Relatives> scopes.
 *
 * Replaces the previous anonymous tuple
 * [$this->repeats, $this->repeat_xml, $this->repeat_line] pushed onto
 * $repeats_stack.
 *
 * - $repeats holds the GEDCOM subrecord strings that the end handler
 *   will iterate over.
 * - $repeat_xml is the captured inner XML of the repeat block, captured
 *   via XMLReader::readInnerXml() and re-parsed per iteration.
 * - $repeat_line is the source line where the block began, used by
 *   error messages to point back at the original report file.
 */
final readonly class RepeatFrame
{
    /**
     * @param array<string> $repeats
     */
    public function __construct(
        public array $repeats,
        public string $repeat_xml,
        public int $repeat_line,
    ) {
    }
}
