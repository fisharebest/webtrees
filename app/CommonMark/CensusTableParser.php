<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\CommonMark;

use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;

use function array_shift;
use function explode;
use function str_starts_with;
use function substr;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 *
 * Based on the table parser from webuni/commonmark-table-extension.
 */
class CensusTableParser implements BlockParserInterface
{
    // Keywords used to create the webtrees 1.x census-assistant notes.
    private const CA_PREFIX = '.start_formatted_area.';
    private const CA_SUFFIX = '.end_formatted_area.';
    private const TH_PREFIX = '.b.';

    /**
     * Parse a paragraph of text with the following structure:
     *
     * .start_formatted_area.
     * .b.HEADING1|.b.HEADING2|.b.HEADING3
     * COL1|COL2|COL3
     * COL1|COL2|COL3
     * .end_formatted_area.
     *
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor): bool
    {
        $container = $context->getContainer();

        if (!$container instanceof Paragraph) {
            return false;
        }

        $lines = $container->getStrings();
        $first = array_shift($lines);

        if ($first !== self::CA_PREFIX) {
            return false;
        }

        if ($cursor->getLine() !== self::CA_SUFFIX) {
            return false;
        }

        // We don't need to parse/markup any of the table's contents.
        $table = new Table(static function (): bool {
            return false;
        });

        // First line is the table header.
        $line = array_shift($lines);
        $row  = $this->parseRow($line, TableCell::TYPE_HEAD);
        $table->getHead()->appendChild($row);

        // Subsequent lines are the table body.
        while ($lines !== []) {
            $line = array_shift($lines);
            $row  = $this->parseRow($line, TableCell::TYPE_BODY);
            $table->getHead()->appendChild($row);
        }

        $context->replaceContainerBlock($table);

        return true;
    }

    /**
     * @param string $line
     * @param string $type
     *
     * @return TableRow
     */
    private function parseRow(string $line, string $type): TableRow
    {
        $cells = explode('|', $line);
        $row   = new TableRow();

        foreach ($cells as $cell) {
            if (str_starts_with($cell, self::TH_PREFIX)) {
                $cell = substr($cell, strlen(self::TH_PREFIX));
                $type = TableCell::TYPE_HEAD;
            }

            $row->appendChild(new TableCell($cell, $type, null));
        }

        return $row;
    }
}
