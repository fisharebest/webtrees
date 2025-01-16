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

namespace Fisharebest\Webtrees\CommonMark;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 */
class CensusTableStartParser implements BlockStartParserInterface
{
    /**
     * @param Cursor                       $cursor
     * @param MarkdownParserStateInterface $parserState
     *
     * @return BlockStart|null
     */
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->getLine() === CensusTableExtension::CA_PREFIX) {
            return BlockStart::of(new CensusTableContinueParser())
                ->at($cursor);
        }

        return BlockStart::none();
    }
}
